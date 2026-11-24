<?php

namespace App\Support;

final class HrAccess
{
    public const ADMIN = ['view-user', 'show-user', 'create-user', 'update-user', 'reset-user-password', 'change-user-status', 'manage-hr-access'];

    public const GROUPS = [
        'Karyawan' => ['view-employee', 'show-employee', 'create-employee', 'update-employee'],
        'Kontrak' => ['view-contract', 'show-contract', 'create-contract', 'update-contract', 'terminate-contract', 'download-employee-contract'],
        'Jabatan' => ['view-position', 'show-position', 'create-position', 'update-position'],
        'Benefit' => ['view-benefit', 'show-benefit', 'create-benefit', 'update-benefit'],
        'Jenis cuti' => ['view-type-leave', 'show-type-leave', 'create-type-leave', 'update-type-leave'],
    ];

    public static function hr(): array
    {
        return array_merge(...array_values(self::GROUPS));
    }

    public static function all(): array
    {
        return array_merge(self::ADMIN, self::hr());
    }
}
