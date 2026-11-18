<?php

it('keeps sample names and transaction codes out of application views', function (): void {
    $root = resource_path('views');
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
    foreach ($files as $file) {
        $path = $file->getPathname();
        if (! str_ends_with($path, '.blade.php')) {
            continue;
        }
        $contents = file_get_contents($path);
        foreach (['Ivan Mark Lizardo', 'Juan Dela Cruz', 'Maria Santos', 'TRX-', 'Sample Food Item', 'Sample Beverage', 'Sample Alcoholic Drink'] as $sample) {
            expect($contents)->not->toContain($sample, "Sample text in {$path}");
        }
    }
});
