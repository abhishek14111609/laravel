<?php

namespace App\Support;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class BookingQrToken
{
    public static function encode(int $bookingId): string
    {
        $payload = implode('|', [
            (string) $bookingId,
            (string) now()->timestamp,
            self::base64UrlEncode(random_bytes(6)),
        ]);

        $signature = self::base64UrlEncode(
            hash_hmac('sha256', $payload, (string) config('app.key'), true)
        );

        return self::base64UrlEncode($payload) . '.' . $signature;
    }

    public static function decode(string $token): int
    {
        // New compact signed format: {base64url(payload)}.{base64url(signature)}
        if (str_contains($token, '.')) {
            [$payloadEncoded, $signature] = explode('.', $token, 2);
            $payload = self::base64UrlDecode($payloadEncoded);

            if ($payload === null || $signature === '') {
                throw new \RuntimeException('Invalid token encoding.');
            }

            $expectedSignature = self::base64UrlEncode(
                hash_hmac('sha256', $payload, (string) config('app.key'), true)
            );

            if (! hash_equals($expectedSignature, $signature)) {
                throw new \RuntimeException('Invalid token signature.');
            }

            $parts = explode('|', $payload);
            if (count($parts) !== 3 || ! ctype_digit($parts[0])) {
                throw new \RuntimeException('Invalid token payload.');
            }

            return (int) $parts[0];
        }

        // Legacy encrypted format kept for backward compatibility.
        $base64 = strtr($token, '-_', '+/');
        $padding = strlen($base64) % 4;
        if ($padding > 0) {
            $base64 .= str_repeat('=', 4 - $padding);
        }

        $encrypted = base64_decode($base64, true);
        if ($encrypted === false) {
            throw new \RuntimeException('Invalid token encoding.');
        }

        $json = Crypt::decryptString($encrypted);
        $payload = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($payload) || ! isset($payload['booking_id'])) {
            throw new \RuntimeException('Invalid token payload.');
        }

        return (int) $payload['booking_id'];
    }

    private static function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $value): ?string
    {
        $base64 = strtr($value, '-_', '+/');
        $padding = strlen($base64) % 4;
        if ($padding > 0) {
            $base64 .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode($base64, true);

        return $decoded === false ? null : $decoded;
    }
}
