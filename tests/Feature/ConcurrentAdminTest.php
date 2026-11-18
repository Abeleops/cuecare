<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

it('keeps an active administrator when two requests race to deactivate each other', function (): void {
    if (DB::getDatabaseName() !== 'cuecare_test') {
        throw new RuntimeException('Concurrency tests must use cuecare_test');
    }

    $suffix = Str::lower(Str::random(8));
    $role = Role::query()->create(['name' => 'Race '.$suffix, 'key' => 'admin']);
    $first = User::query()->create([
        'staff_code' => 'RACE-A-'.$suffix, 'role_id' => $role->id,
        'full_name' => 'Race A', 'username' => 'race-a-'.$suffix,
        'password' => 'password123', 'is_active' => true,
    ]);
    $second = User::query()->create([
        'staff_code' => 'RACE-B-'.$suffix, 'role_id' => $role->id,
        'full_name' => 'Race B', 'username' => 'race-b-'.$suffix,
        'password' => 'password123', 'is_active' => true,
    ]);
    $prefix = sys_get_temp_dir().DIRECTORY_SEPARATOR.'cuecare-race-'.$suffix;
    $markers = [$prefix.'-a', $prefix.'-b', $prefix.'-go'];
    $worker = <<<'PHP'
require getcwd().'/vendor/autoload.php';
$app = require getcwd().'/bootstrap/app.php';
$app->instance('request', Illuminate\Http\Request::create('/'));
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();
auth()->guard('web')->setUser(App\Models\User::query()->findOrFail($argv[1]));
file_put_contents($argv[3], 'ready');
$deadline = microtime(true) + 12;
while (! file_exists($argv[4])) {
    if (microtime(true) > $deadline) { exit(2); }
    usleep(10000);
}
$target = App\Models\User::query()->findOrFail($argv[2]);
$request = Illuminate\Http\Request::create('/staff/'.$target->id, 'PUT', [
    'full_name' => $target->full_name,
    'username' => $target->username,
    'role' => 'admin',
    'is_active' => '0',
]);
$request->headers->set('Accept', 'application/json');
$response = $kernel->handle($request);
echo $response->getStatusCode();
$kernel->terminate($request, $response);
PHP;
    $environment = [
        'APP_ENV' => 'testing', 'DB_CONNECTION' => 'mysql', 'DB_DATABASE' => 'cuecare_test',
        'DB_URL' => '', 'SESSION_DRIVER' => 'array', 'CACHE_STORE' => 'array',
    ];
    $processes = [
        new Process([PHP_BINARY, '-r', $worker, (string) $first->id, (string) $second->id, $markers[0], $markers[2]], base_path(), $environment),
        new Process([PHP_BINARY, '-r', $worker, (string) $second->id, (string) $first->id, $markers[1], $markers[2]], base_path(), $environment),
    ];

    try {
        // Hold the role row while both requests start so they contend for the same lock
        DB::beginTransaction();
        Role::query()->whereKey($role->id)->lockForUpdate()->firstOrFail();
        foreach ($processes as $process) {
            $process->setTimeout(15);
            $process->start();
        }
        $deadline = microtime(true) + 10;
        while (! file_exists($markers[0]) || ! file_exists($markers[1])) {
            foreach ($processes as $process) {
                if (! $process->isRunning()) {
                    throw new RuntimeException('Concurrent worker failed: '.$process->getErrorOutput().' '.$process->getOutput());
                }
            }
            if (microtime(true) > $deadline) {
                throw new RuntimeException('Concurrent workers did not start');
            }
            usleep(10000);
        }
        file_put_contents($markers[2], 'go');
        usleep(500000);
        expect($processes[0]->isRunning())->toBeTrue();
        expect($processes[1]->isRunning())->toBeTrue();
        DB::commit();

        $statuses = [];
        foreach ($processes as $process) {
            $process->wait();
            expect($process->getExitCode())->toBe(0, $process->getErrorOutput());
            $statuses[] = (int) trim($process->getOutput());
        }
        sort($statuses);
        expect($statuses)->toBe([302, 422]);
        expect(User::query()->where('role_id', $role->id)->where('is_active', true)->count())->toBe(1);
    } finally {
        foreach ($processes as $process) {
            if ($process->isRunning()) {
                $process->stop();
            }
        }
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }
        DB::table('audit_logs')->whereIn('subject_id', [$first->id, $second->id])
            ->where('subject_type', (new User)->getMorphClass())
            ->whereIn('action', ['staff_updated', 'staff_deactivated'])->delete();
        User::withTrashed()->whereIn('id', [$first->id, $second->id])->forceDelete();
        $role->delete();
        foreach ($markers as $marker) {
            if (file_exists($marker)) {
                unlink($marker);
            }
        }
    }
});
