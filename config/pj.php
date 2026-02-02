<?php

return [
    'hourly_rate' => env('HOURLY_RATE', 150),
    'extra_home_office' => env('EXTRA_HOME_OFFICE', 0),

    'cnpjs' => [
        1 => [
            'name' => env('CNPJ_1_NAME', 'Empresa Alpha LTDA'),
            'number' => env('CNPJ_1_NUMBER', '12.345.678/0001-90'),
        ],
        2 => [
            'name' => env('CNPJ_2_NAME', 'Empresa Beta ME'),
            'number' => env('CNPJ_2_NUMBER', '98.765.432/0001-10'),
        ],
        3 => [
            'name' => env('CNPJ_3_NAME', 'Empresa Gamma EIRELI'),
            'number' => env('CNPJ_3_NUMBER', '11.222.333/0001-44'),
        ],
    ],
];
