<?php

namespace Blu3blaze\Encrypter;

use Throwable;
use SensitiveParameter;
use InvalidArgumentException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Contracts\Encryption\StringEncrypter;
use Illuminate\Contracts\Encryption\Encrypter as EncrypterContract;

class Encrypter implements EncrypterContract, StringEncrypter {
    private string $key;

    public function __construct(string $key) {
        if (strlen($key) !== 32) {
            throw new InvalidArgumentException(
                message: sprintf("Key must be exactly %d bytes", 32)
            );
        }

        $this->key = $key;
    }

    /**
     * @param mixed $value
     * @param bool $serialize
     *
     * @return string
     * @throws EncryptException
     */
    public function encrypt(#[SensitiveParameter] $value, $serialize = true): string {
        try {
            $nonce = random_bytes(length: 24);

            $ciphertext = sodium_crypto_aead_xchacha20poly1305_ietf_encrypt(
                message: $serialize ? serialize($value) : $value,
                additional_data: $nonce,
                nonce: $nonce,
                key: $this->key
            );

            $token = sodium_bin2base64($nonce . $ciphertext, 7);
        }catch (Throwable) {
            throw new EncryptException('Could not encrypt the data.');
        }

        return $token;
    }

    /**
     * @param string $payload
     * @param bool $unserialize
     *
     * @return mixed
     * @throws DecryptException
     */
    public function decrypt($payload, $unserialize = true): mixed {
        try {
            $token = sodium_base642bin($payload, 7);
            $nonce = substr($token, 0, 24);

            $decrypted = sodium_crypto_aead_xchacha20poly1305_ietf_decrypt(
                ciphertext: substr($token, 24),
                additional_data: $nonce,
                nonce: $nonce,
                key: $this->key
            );
        } catch (Throwable) {
            throw new DecryptException('Could not decrypt the data.');
        }

        if ($decrypted === false) {
            throw new DecryptException('Could not decrypt the data.');
        }

        return $unserialize ? unserialize($decrypted) : $decrypted;
    }

    /**
     * @return string
     */
    public function getKey(): string {
        return $this->key;
    }

    /**
     * @return string[]
     */
    public function getAllKeys(): array {
        return [$this->key];
    }

    /**
     * @return string[]
     */
    public function getPreviousKeys(): array {
        return [];
    }

    /**
     * @param string $value
     *
     * @return string
     * @throws EncryptException
     */
    public function encryptString(#[SensitiveParameter] $value): string {
        return $this->encrypt($value, serialize: false);
    }

    /**
     * @param string $payload
     *
     * @return string
     * @throws DecryptException
     */
    public function decryptString($payload): string {
        return $this->decrypt($payload, unserialize: false);
    }
}