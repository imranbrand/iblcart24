<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    protected $signature = 'shop:backup {--keep= : Days to keep old backups (default from BACKUP_KEEP_DAYS)}';

    protected $description = 'Dump the MySQL database to storage/app/backups and delete old backups';

    public function handle(): int
    {
        $connection = config('database.default');
        $driver     = config("database.connections.{$connection}.driver");

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            $this->error("shop:backup supports MySQL/MariaDB only (current driver: {$driver}).");

            return self::FAILURE;
        }

        $dir = (string) config('logistics.backup.path');
        File::ensureDirectoryExists($dir);

        $file = $dir . DIRECTORY_SEPARATOR . 'db-' . now()->format('Ymd-His') . '.sql';
        $fh   = fopen($file, 'w');

        if ($fh === false) {
            $this->error('Cannot write to ' . $dir);

            return self::FAILURE;
        }

        $pdo = DB::connection()->getPdo();

        fwrite($fh, '-- IBL CART 24 database backup ' . now()->toDateTimeString() . "\n");
        fwrite($fh, "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n\n");

        $tables = DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');

        foreach ($tables as $row) {
            $name = (string) array_values((array) $row)[0];

            $create    = DB::select("SHOW CREATE TABLE `{$name}`");
            $createSql = (string) array_values((array) $create[0])[1];

            fwrite($fh, "DROP TABLE IF EXISTS `{$name}`;\n{$createSql};\n\n");

            $batch   = [];
            $columns = null;

            foreach (DB::table($name)->cursor() as $record) {
                $record = (array) $record;

                if ($columns === null) {
                    $columns = '`' . implode('`,`', array_keys($record)) . '`';
                }

                $values = array_map(
                    fn ($value) => $value === null ? 'NULL' : $pdo->quote((string) $value),
                    array_values($record)
                );

                $batch[] = '(' . implode(',', $values) . ')';

                if (count($batch) >= 200) {
                    fwrite($fh, "INSERT INTO `{$name}` ({$columns}) VALUES\n" . implode(",\n", $batch) . ";\n");
                    $batch = [];
                }
            }

            if ($batch !== []) {
                fwrite($fh, "INSERT INTO `{$name}` ({$columns}) VALUES\n" . implode(",\n", $batch) . ";\n");
            }

            fwrite($fh, "\n");
        }

        fwrite($fh, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($fh);

        $this->info('Backup saved: ' . $file . ' (' . number_format(filesize($file) / 1024, 1) . ' KB)');

        $this->pruneOldBackups($dir);

        return self::SUCCESS;
    }

    protected function pruneOldBackups(string $dir): void
    {
        $keep = $this->option('keep');
        $days = $keep !== null && $keep !== '' ? (int) $keep : (int) config('logistics.backup.keep_days', 14);

        if ($days < 1) {
            return;
        }

        $limit   = now()->subDays($days)->getTimestamp();
        $removed = 0;

        foreach (File::files($dir) as $backup) {
            if (str_starts_with($backup->getFilename(), 'db-') && $backup->getMTime() < $limit) {
                File::delete($backup->getPathname());
                $removed++;
            }
        }

        if ($removed > 0) {
            $this->line("Removed {$removed} backup(s) older than {$days} days.");
        }
    }
}
