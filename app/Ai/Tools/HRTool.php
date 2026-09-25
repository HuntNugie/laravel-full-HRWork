<?php

namespace App\Ai\Tools;

use App\Models\Employees;
use Illuminate\Database\Eloquent\Model;
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

    /**
     * Resolve a human-readable employee identity into the canonical employee id.
     *
     * The AI may carry context between turns, but internal ids should never be
     * treated as the only identity. Prefer employee_code or query (name/email),
     * and verify an employee_id against that identity when more than one value
     * is supplied.
     *
     * @return array{employee_id:int|null,error:string|null,message:string|null}
     */
    protected function resolveEmployee(
        mixed $employeeId = null,
        string $employeeCode = '',
        string $query = ''
    ): array {
        $employeeId = $employeeId !== null && $employeeId !== ''
            ? (int) $employeeId
            : null;

        $employeeCode = $this->value($employeeCode);
        $query = $this->value($query);

        if ($employeeCode !== '') {
            $employee = Employees::query()
                ->with('user:id,name,email')
                ->where('employee_code', $employeeCode)
                ->first();

            if (! $employee) {
                return [
                    'employee_id' => null,
                    'error' => 'employee_not_found',
                    'message' => "Karyawan dengan kode {$employeeCode} tidak ditemukan.",
                ];
            }

            if ($employeeId !== null && $employee->id !== $employeeId) {
                return [
                    'employee_id' => null,
                    'error' => 'employee_identity_mismatch',
                    'message' => 'employee_id tidak sesuai dengan employee_code yang diberikan. Gunakan identitas karyawan yang sama.',
                ];
            }

            return [
                'employee_id' => $employee->id,
                'error' => null,
                'message' => null,
            ];
        }

        if ($query !== '') {
            $employees = Employees::query()
                ->with('user:id,name,email')
                ->where(function ($builder) use ($query) {
                    $builder
                        ->where('employee_code', $query)
                        ->orWhereHas('user', function ($userQuery) use ($query) {
                            $userQuery
                                ->where('name', $query)
                                ->orWhere('email', $query);
                        });
                })
                ->limit(5)
                ->get();

            if ($employees->count() === 0) {
                return [
                    'employee_id' => null,
                    'error' => 'employee_not_found',
                    'message' => 'Karyawan yang dimaksud tidak ditemukan.',
                ];
            }

            if ($employees->count() > 1) {
                return [
                    'employee_id' => null,
                    'error' => 'employee_ambiguous',
                    'message' => 'Identitas karyawan tidak cukup spesifik. Gunakan nama lengkap, email, atau kode karyawan.',
                ];
            }

            $employee = $employees->first();

            if ($employeeId !== null && $employee->id !== $employeeId) {
                return [
                    'employee_id' => null,
                    'error' => 'employee_identity_mismatch',
                    'message' => 'employee_id tidak sesuai dengan identitas karyawan yang diberikan.',
                ];
            }

            return [
                'employee_id' => $employee->id,
                'error' => null,
                'message' => null,
            ];
        }

        if ($employeeId !== null) {
            $employee = Employees::query()->find($employeeId);

            if (! $employee) {
                return [
                    'employee_id' => null,
                    'error' => 'employee_not_found',
                    'message' => 'Employee ID tidak ditemukan.',
                ];
            }

            return [
                'employee_id' => $employee->id,
                'error' => null,
                'message' => null,
            ];
        }

        return [
            'employee_id' => null,
            'error' => null,
            'message' => null,
        ];
    }
}
