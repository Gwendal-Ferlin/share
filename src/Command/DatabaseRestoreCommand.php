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
    name: 'app:database:restore',
    description: 'Restores the MariaDB database from an encrypted backup file.',
)]
class DatabaseRestoreCommand extends Command
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
        
        // Paramètres pour récupérer le fichier depuis le serveur distant
        $this->remoteUser = $params->get('remote_user');
        $this->remoteHost = $params->get('remote_host');
        $this->remotePath = rtrim($params->get('remote_path'), '/');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Étape 1 : Télécharger le fichier de sauvegarde chiffré depuis le serveur distant
        $latestEncryptedFile = "{$this->remotePath}/backup_latest.sql.enc";
        $localEncryptedFile = "{$this->backupDir}/backup_latest.sql.enc";

        $scpCommand = sprintf(
            'scp %s@%s:%s %s',
            escapeshellarg($this->remoteUser),
            escapeshellarg($this->remoteHost),
            escapeshellarg($latestEncryptedFile),
            escapeshellarg($localEncryptedFile)
        );

        $scpProcess = Process::fromShellCommandline($scpCommand);
        $scpProcess->run();

        if (!$scpProcess->isSuccessful()) {
            $output->writeln('<error>Failed to download the encrypted backup file from the remote server.</error>');
            return Command::FAILURE;
        }

        $output->writeln("<info>Encrypted backup file downloaded to {$localEncryptedFile}</info>");

        // Étape 2 : Déchiffrer le fichier de sauvegarde
        $decryptedFile = $this->encryptionService->decryptFile($localEncryptedFile);
        if (!$decryptedFile) {
            $output->writeln('<error>Failed to decrypt the backup file.</error>');
            return Command::FAILURE;
        }

        $output->writeln("<info>Decrypted backup file created at {$decryptedFile}</info>");

        // Étape 3 : Restaurer la base de données
        $urlParts = parse_url($this->databaseUrl);
        $user = $urlParts['user'] ?? '';
        $password = $urlParts['pass'] ?? '';
        $host = $urlParts['host'] ?? '127.0.0.1';
        $port = $urlParts['port'] ?? '3306';
        $dbname = ltrim($urlParts['path'], '/');

        $command = sprintf(
            'mysql -h %s -P %s -u %s -p%s %s < %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($user),
            escapeshellarg($password),
            escapeshellarg($dbname),
            escapeshellarg($decryptedFile)
        );

        $process = Process::fromShellCommandline($command);
        $process->run();

        if (!$process->isSuccessful()) {
            $output->writeln('<error>Failed to restore the database.</error>');
            return Command::FAILURE;
        }

        $output->writeln("<info>Database successfully restored from {$decryptedFile}</info>");
        return Command::SUCCESS;
    }
}
