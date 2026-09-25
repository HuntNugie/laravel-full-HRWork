<?php

namespace App\Ai\Tools;

use Stringable;

abstract class HRTool
{
    protected function denied(string $permission): ?string
    {
        $user = auth()->user();

        if (! $user || ! $user->can($permission)) {
            return $this->json([
                'success' => false,
                'message' => 'Pengguna tidak memiliki izin untuk mengakses data HRWork yang diminta.',
            ]);
        }

        return null;
    }

    protected function json(array $payload): string
    {
        return json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }

    protected function value(mixed $value, string $default = ''): string
    {
        return trim((string) ($value ?? $default));
    }

    protected function optionalDate(mixed $value): ?string
    {
        $value = $this->value($value);

        return $value !== '' ? $value : null;
    }
}
