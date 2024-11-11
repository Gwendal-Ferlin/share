<?php

namespace App\Service;

class EncryptionService
{
    private string $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function encryptFile(string $filePath): string
    {
        $encryptedFilePath = $filePath . '.enc';
        $data = file_get_contents($filePath);
        $encryptedData = openssl_encrypt($data, 'aes-256-cbc', $this->key, 0, substr($this->key, 0, 16));
        file_put_contents($encryptedFilePath, $encryptedData);
        unlink($filePath); 
        return $encryptedFilePath;
    }

    public function decryptFile(string $encryptedFilePath): string
    {
        $decryptedFilePath = str_replace('.enc', '', $encryptedFilePath);
        $data = file_get_contents($encryptedFilePath);
        $decryptedData = openssl_decrypt($data, 'aes-256-cbc', $this->key, 0, substr($this->key, 0, 16));
        file_put_contents($decryptedFilePath, $decryptedData);
        return $decryptedFilePath;
    }
}
