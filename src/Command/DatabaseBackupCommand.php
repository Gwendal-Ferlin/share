<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Process\Process;
use App\Service\EncryptionService;

#[AsCommand(
    name: 'app:database:backup',
    description: 'Exports the MariaDB database to a SQL dump file, encrypts it, and transfers it to a remote server.',
)]
class DatabaseBackupCommand extends Command
{
    private string $databaseUrl;
    private string $backupDir;
    private EncryptionService $encryptionService;
    private string $remoteUser;
    private string $remoteHost;
    private string $remotePath;

    public function __construct(ParameterBagInterface $params, EncryptionService $encryptionService)
    {
        parent::__construct();
        $this->databaseUrl = $_ENV['DATABASE_URL'] ?? $_SERVER['DATABASE_URL'] ?? $params->get('DATABASE_URL');
        $this->backupDir = $params->get('file_directory') . '/backups';
        $this->encryptionService = $encryptionService;
        
        // Ajoute les paramètres pour le transfert SCP
        $this->remoteUser = $params->get('remote_user');
        $this->remoteHost = $params->get('remote_host');
        $this->remotePath = rtrim($params->get('remote_path'), '/'); // Suppression du '/' à la fin si présent
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Étape 1 : Exporter la base de données
        $urlParts = parse_url($this->databaseUrl);
        $user = $urlParts['user'] ?? '';
        $password = $urlParts['pass'] ?? '';
        $host = $urlParts['host'] ?? '127.0.0.1';
        $port = $urlParts['port'] ?? '3306';
        $dbname = ltrim($urlParts['path'], '/');

        $timestamp = (new \DateTime())->format('Y-m-d_H-i-s');
        $backupFile = "{$this->backupDir}/backup_{$timestamp}.sql";

        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }

        $command = sprintf(
            'mysqldump -h %s -P %s -u %s -p%s %s > %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($user),
            escapeshellarg($password),
            escapeshellarg($dbname),
            escapeshellarg($backupFile)
        );

        $process = Process::fromShellCommandline($command);
        $process->run();

        if (!$process->isSuccessful()) {
            $output->writeln('<error>Failed to export database.</error>');
            return Command::FAILURE;
        }

        $output->writeln("<info>Database exported to {$backupFile}</info>");

        // Étape 2 : Chiffrer le fichier
        $encryptedFile = $this->encryptionService->encryptFile($backupFile);
        $output->writeln("<info>Encrypted backup created at {$encryptedFile}</info>");

        // Calcul du hash SHA-256 du fichier chiffré
        $hashProcess = Process::fromShellCommandline("sha256sum " . escapeshellarg($encryptedFile));
        $hashProcess->run();
        $localHash = explode(" ", $hashProcess->getOutput())[0];
        $output->writeln("<info>Local file hash: {$localHash}</info>");

        // Étape 3 : Transférer le fichier chiffré avec SCP
        $scpCommand = sprintf(
            'scp %s %s@%s:%s/',
            escapeshellarg($encryptedFile),
            escapeshellarg($this->remoteUser),
            escapeshellarg($this->remoteHost),
            escapeshellarg($this->remotePath)
        );

        $scpProcess = Process::fromShellCommandline($scpCommand);
        $scpProcess->run();

        if (!$scpProcess->isSuccessful()) {
            $output->writeln('<error>Failed to transfer the encrypted backup file to the remote server.</error>');
            return Command::FAILURE;
        }

        $output->writeln("<info>Encrypted backup file transferred to {$this->remoteHost}:{$this->remotePath}</info>");

        // Étape 4 : Vérification de l’intégrité du fichier sur le serveur distant
        $remoteFilePath = "{$this->remotePath}/" . basename($encryptedFile);
        $sshCommand = sprintf(
            'ssh %s@%s "sha256sum %s"',
            escapeshellarg($this->remoteUser),
            escapeshellarg($this->remoteHost),
            escapeshellarg($remoteFilePath)
        );

        $sshProcess = Process::fromShellCommandline($sshCommand);
        $sshProcess->run();
        $remoteHash = explode(" ", $sshProcess->getOutput())[0];
        $output->writeln("<info>Remote file hash: {$remoteHash}</info>");

        if ($localHash === $remoteHash) {
            $output->writeln("<info>Integrity check passed: the file on the remote server is identical to the local file.</info>");
            return Command::SUCCESS;
        } else {
            $output->writeln("<error>Integrity check failed: the file on the remote server does not match the local file.</error>");
            return Command::FAILURE;
        }
    }
}
