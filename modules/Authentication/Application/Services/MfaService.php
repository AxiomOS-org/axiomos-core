<?php

declare(strict_types=1);

namespace Modules\Authentication\Application\Services;

use Modules\Authentication\Domain\Models\AuthMfaMethod;

final class MfaService
{
    /**
     * @return array{secret: string, recovery_codes: list<string>}
     */
    public function enableTotp(string $userId): array
    {
        $secret = $this->generateBase32Secret(32);
        $recoveryCodes = [];
        for ($i = 0; $i < 8; $i++) {
            $recoveryCodes[] = strtoupper(bin2hex(random_bytes(4)));
        }

        AuthMfaMethod::query()->updateOrCreate(
            ['user_id' => $userId, 'method_type' => 'totp'],
            [
                'secret_encrypted' => base64_encode($secret),
                'enabled' => true,
                'recovery_codes' => $recoveryCodes,
                'status' => 'active',
            ],
        );

        return ['secret' => $secret, 'recovery_codes' => $recoveryCodes];
    }

    public function verifyChallenge(string $userId, string $code): bool
    {
        $method = AuthMfaMethod::query()
            ->where('user_id', $userId)
            ->where('method_type', 'totp')
            ->where('enabled', true)
            ->first();

        if ($method === null) {
            return false;
        }

        $secret = (string) base64_decode((string) $method->secret_encrypted, true);
        $counter = (int) floor(time() / 30);

        foreach ([-1, 0, 1] as $offset) {
            if (hash_equals($this->hotp($secret, $counter + $offset), $code)) {
                return true;
            }
        }

        return false;
    }

    private function generateBase32Secret(int $length): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return $secret;
    }

    private function hotp(string $base32Secret, int $counter): string
    {
        $binaryKey = $this->decodeBase32($base32Secret);
        $binCounter = pack('N*', 0) . pack('N*', $counter);
        $hash = hash_hmac('sha1', $binCounter, $binaryKey, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $truncated = substr($hash, $offset, 4);
        $value = unpack('N', $truncated)[1] & 0x7FFFFFFF;

        return str_pad((string) ($value % 1000000), 6, '0', STR_PAD_LEFT);
    }

    private function decodeBase32(string $value): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $bits = '';
        foreach (str_split($value) as $char) {
            $position = strpos($alphabet, strtoupper($char));
            if ($position === false) {
                continue;
            }
            $bits .= str_pad(decbin($position), 5, '0', STR_PAD_LEFT);
        }

        $binary = '';
        foreach (str_split($bits, 8) as $chunk) {
            if (strlen($chunk) === 8) {
                $binary .= chr(bindec($chunk));
            }
        }

        return $binary;
    }
}
