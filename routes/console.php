<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jibble sync disabled for HRM module; attendance remains manual/internal
// Schedule::command('jibble:sync --all')
//     ->twiceDaily(8, 18)
//     ->timezone('Asia/Kathmandu')
//     ->withoutOverlapping()
//     ->runInBackground();

// Baseline existing schema into migrations table without data changes
Artisan::command('migrate:baseline {--batch=} {--dry} {--force}', function () {
    if (!$this->option('force')) {
        $this->warn('Use --force to confirm baselining existing schema.');
        return 1;
    }

    $migrationsDir = database_path('migrations');
    $files = collect(File::files($migrationsDir))
        ->sortBy(fn($f) => $f->getFilename())
        ->values();

    $existing = DB::table('migrations')->pluck('migration')->all();
    $nextBatch = $this->option('batch') ? (int) $this->option('batch') : ((DB::table('migrations')->max('batch') ?? 0) + 1);

    $baselined = [];
    $skipped = [];

    foreach ($files as $file) {
        $name = pathinfo($file->getFilename(), PATHINFO_FILENAME);
        if (in_array($name, $existing, true)) {
            $skipped[] = [$name, 'already recorded'];
            continue;
        }

        $contents = File::get($file->getPathname());
        preg_match_all("/Schema::create\(['\"](\w+)['\"]/", $contents, $matches);
        $tables = $matches[1] ?? [];

        if (empty($tables)) {
            $skipped[] = [$name, 'no create() detected'];
            continue;
        }

        $allExist = collect($tables)->every(fn($t) => Schema::hasTable($t));
        if ($allExist) {
            if (!$this->option('dry')) {
                DB::table('migrations')->insert([
                    'migration' => $name,
                    'batch' => $nextBatch,
                ]);
            }
            $baselined[] = $name;
        } else {
            $missing = collect($tables)->filter(fn($t) => !Schema::hasTable($t))->values()->all();
            $skipped[] = [$name, 'missing tables: ' . implode(', ', $missing)];
        }
    }

    $this->info('Baseline complete.');
    $this->table(['Status', 'Migration', 'Note'], array_merge(
        array_map(fn($m) => ['BASELINED', $m, ''], $baselined),
        array_map(fn($s) => ['SKIPPED', $s[0], $s[1]], $skipped)
    ));

    return 0;
})->purpose('Mark migrations as ran when their tables already exist (no schema changes).');
