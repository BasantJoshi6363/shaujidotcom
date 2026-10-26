<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;


class OtpService
{
    public const TTL_MINUTES = 10;
    public const MAX_ATTEMPTS = 5;
    public const RESEND_SECONDS = 60;

    /** Returns the plain code to email, or false if the resend cooldown is active. */
    public function issue(string $purpose, string $email): string|false
    {
        $key = $this->key($purpose, $email);

        if (Cache::has($key . ':cooldown')) {
            return false;
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put($key, [
            'hash' => Hash::make($code),
            'attempts' => 0,
        ], now()->addMinutes(self::TTL_MINUTES));

        Cache::put($key . ':cooldown', true, now()->addSeconds(self::RESEND_SECONDS));

        return $code;
    }

    public function verify(string $purpose, string $email, string $code): bool
    {
        $key = $this->key($purpose, $email);
        $data = Cache::get($key);

        if (!$data) {
            return false;
        }

        if ($data['attempts'] >= self::MAX_ATTEMPTS) {
            Cache::forget($key);
            return false;
        }

        if (!Hash::check($code, $data['hash'])) {
            $data['attempts']++;
            Cache::put($key, $data, now()->addMinutes(self::TTL_MINUTES));
            return false;
        }

        Cache::forget($key);
        return true;
    }

    private function key(string $purpose, string $email): string
    {
        return 'otp:' . $purpose . ':' . sha1(strtolower(trim($email)));
    }
}