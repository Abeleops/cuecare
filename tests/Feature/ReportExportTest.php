<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

uses(DatabaseTransactions::class);

it('forbids cashiers from exporting reports', function (): void {
    $user = (new User)->forceFill(['id' => 1, 'is_active' => true]);
    $user->setRelation('role', (new Role)->forceFill(['key' => 'cashier']));
    $this->actingAs($user)->get(route('reports.export', ['type' => 'sales']))->assertForbidden();
});

it('rejects unsupported export types', function (): void {
    $user = (new User)->forceFill(['id' => 1, 'is_active' => true]);
    $user->setRelation('role', (new Role)->forceFill(['key' => 'admin']));
    $this->actingAs($user)->getJson(route('reports.export', ['type' => 'pdf']))->assertUnprocessable()->assertJsonValidationErrors('type');
});

it('exports only completed sales matching the selected filters', function (): void {
    DB::table('roles')->insert(['id' => 1, 'name' => 'Admin', 'key' => 'admin']);
    DB::table('users')->insert(['id' => 1, 'staff_code' => 'TEST', 'role_id' => 1, 'username' => 'admin', 'password' => 'unused', 'full_name' => '=Formula']);
    DB::table('sales')->insert([
        [
            'transaction_code' => 'TRX-000001', 'sold_by' => 1,
            'subtotal' => 125, 'amount_received' => 125, 'gcash_reference' => null,
            'status' => 'completed', 'payment_method' => 'cash', 'total_amount' => 125,
            'completed_at' => '2025-03-10 12:00:00',
        ],
        [
            'transaction_code' => 'TRX-000002', 'sold_by' => 1,
            'subtotal' => 75, 'amount_received' => 75, 'gcash_reference' => 'ABC123',
            'status' => 'completed', 'payment_method' => 'gcash', 'total_amount' => 75,
            'completed_at' => '2025-03-10 12:00:00',
        ],
    ]);
    $user = (new User)->forceFill(['id' => 1, 'is_active' => true]);
    $user->setRelation('role', (new Role)->forceFill(['key' => 'admin']));
    $response = $this->actingAs($user)->get(route('reports.export', ['type' => 'sales', 'start_date' => '2025-03-01', 'end_date' => '2025-03-30', 'payment' => 'cash']));
    $response->assertOk()->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
        ->assertHeader('Content-Disposition', 'attachment; filename="sales-report.xlsx"');
    $sheet = readReportWorkbook($response->getContent())->getActiveSheet();
    expect($sheet->getCell('A6')->getValue())->toBe('TRX-000001')
        ->and($sheet->getCell('A7')->getValue())->toBeNull()
        ->and($sheet->getCell('C6')->getValue())->toBe('=Formula')
        ->and($sheet->getCell('C6')->getDataType())->toBe(DataType::TYPE_STRING)
        ->and($sheet->getCell('D6')->getValue())->toEqual(125)
        ->and($sheet->getCell('D6')->getDataType())->toBe(DataType::TYPE_NUMERIC)
        ->and($sheet->getCell('A5')->getValue())->toBe('Transaction Code')
        ->and($sheet->getFreezePane())->toBe('A6')
        ->and($sheet->getStyle('A5')->getFont()->getBold())->toBeTrue()
        ->and($sheet->getStyle('A5')->getFill()->getStartColor()->getRGB())->toBe('FFB800')
        ->and($sheet->getStyle('D6')->getNumberFormat()->getFormatCode())->toContain('₱')
        ->and($sheet->getCell('A2')->getValue())->toContain('2025-03-01', '2025-03-30', 'cash');
    $this->assertDatabaseHas('audit_logs', ['action' => 'export']);
});
