<?php

namespace App\Console\Commands;

use App\Mail\BackupMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use ZipArchive;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Faz backup do banco de dados e envia por e-mail';

    public function handle(): int
    {
        $connection = config('database.default');
        $timestamp  = now()->format('Y-m-d_H-i-s');
        $backupDir  = storage_path('app/backups');

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $zipName  = "backup-{$timestamp}.zip";
        $zipPath  = "{$backupDir}/{$zipName}";
        $dumpPath = null;

        try {
            if ($connection === 'sqlite') {
                $dumpPath = $this->dumpSqlite();
            } elseif ($connection === 'mysql') {
                $dumpPath = $this->dumpMysql($backupDir, $timestamp);
            } else {
                $this->error("Conexão não suportada: {$connection}");
                return self::FAILURE;
            }

            $this->createZip(
                $zipPath,
                $dumpPath,
                $connection === 'sqlite' ? 'database.sqlite' : 'database.sql'
            );

            $sizeKb = round(filesize($zipPath) / 1024, 2);
            $this->info("ZIP criado: {$zipName} ({$sizeKb} KB)");

            $recipients = array_filter(array_map('trim', explode(',', config('services.backup.email', ''))));

            if (empty($recipients)) {
                throw new \RuntimeException('Nenhum e-mail configurado em BACKUP_EMAIL.');
            }

            Mail::to($recipients)->send(new BackupMail($zipPath, $zipName, $sizeKb, $connection));
            $this->info('E-mail enviado para: ' . implode(', ', $recipients));

            $this->saveStatus('success', $zipName, $sizeKb);
            Log::channel('daily')->info("Backup realizado: {$zipName}", [
                'connection' => $connection,
                'size_kb'    => $sizeKb,
                'recipients' => $recipients,
            ]);
        } catch (\Throwable $e) {
            $this->error('Falha no backup: ' . $e->getMessage());
            Log::channel('daily')->error('Falha no backup do banco de dados', ['error' => $e->getMessage()]);
            $this->saveStatus('error', $zipName ?? null, 0, $e->getMessage());
            return self::FAILURE;
        } finally {
            if ($dumpPath && file_exists($dumpPath) && $dumpPath !== database_path('database.sqlite')) {
                unlink($dumpPath);
            }
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }
        }

        return self::SUCCESS;
    }

    private function dumpSqlite(): string
    {
        $path = database_path('database.sqlite');

        if (!file_exists($path)) {
            throw new \RuntimeException("Arquivo SQLite não encontrado: {$path}");
        }

        return $path;
    }

    private function dumpMysql(string $backupDir, string $timestamp): string
    {
        $host     = config('database.connections.mysql.host');
        $port     = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $dumpPath = "{$backupDir}/database-{$timestamp}.sql";

        $args = implode(' ', [
            '--no-tablespaces',
            '--single-transaction',
            '--quick',
            '--lock-tables=false',
            '--routines',
            '--triggers',
            '--add-drop-table',
            '--host=' . escapeshellarg($host),
            '--port=' . escapeshellarg((string) $port),
            '--user=' . escapeshellarg($username),
            escapeshellarg($database),
        ]);

        $env     = !empty($password) ? 'MYSQL_PWD=' . escapeshellarg($password) . ' ' : '';
        $command = "{$env}mysqldump {$args} > " . escapeshellarg($dumpPath) . ' 2>&1';

        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            throw new \RuntimeException('mysqldump falhou (código ' . $exitCode . '): ' . implode("\n", $output));
        }

        if (!file_exists($dumpPath) || filesize($dumpPath) === 0) {
            throw new \RuntimeException('O dump MySQL gerado está vazio.');
        }

        return $dumpPath;
    }

    private function createZip(string $zipPath, string $sourcePath, string $nameInZip): void
    {
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Não foi possível criar o arquivo ZIP: {$zipPath}");
        }

        $zip->addFile($sourcePath, $nameInZip);
        $zip->close();
    }

    private function saveStatus(string $status, ?string $fileName, float $sizeKb, ?string $error = null): void
    {
        file_put_contents(
            storage_path('app/backup-status.json'),
            json_encode([
                'status'      => $status,
                'file'        => $fileName,
                'size_kb'     => $sizeKb,
                'error'       => $error,
                'executed_at' => now()->toIso8601String(),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
}
