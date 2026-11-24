<?php

return [
    'admin_email' => env('HRWORK_ADMIN_EMAIL'),
    'admin_password' => env('HRWORK_ADMIN_PASSWORD'),
    // Existing PDF assumption (Mon-Sat), configurable after company confirmation.
    'contract_workdays' => [1, 2, 3, 4, 5, 6],
];
