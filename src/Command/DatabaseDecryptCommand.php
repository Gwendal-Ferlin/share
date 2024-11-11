<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Service\EncryptionService;

#[AsCommand(
    name: 'app:database:decrypt',
    description: 'Decrypts the latest encrypted database backup file.',
)]
class DatabaseDecryptCommand extends Command
{
    private string $backupDir;
    private EncryptionService $encryptionService;

    public function __construct(ParameterBagInterface $params, EncryptionService $encryptionService)
    {
        parent::__construct();
        $this->backupDir = $params->get('file_directory') . '/backups';
        $this->encryptionService = $encryptionService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Récupère le fichier de sauvegarde chiffré le plus récent
        $files = glob("{$this->backupDir}/*.sql.enc");
        if (empty($files)) {
            $output->writeln('<error>No encrypted backup files found.</error>');
            return Command::FAILURE;
        }

        $latestEncryptedFile = end($files);
        $output->writeln("<info>Decrypting file: {$latestEncryptedFile}</info>");

        // Déchiffre le fichier
        $decryptedFile = $this->encryptionService->decryptFile($latestEncryptedFile);
        $output->writeln("<info>Decrypted file created at {$decryptedFile}</info>");

        return Command::SUCCESS;
    }
}
