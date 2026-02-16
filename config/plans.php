<?php

return [
    'trial_days' => 7,

    'prices' => [
        1 => ['price' => 9.90, 'label' => '1 mês'],
        3 => ['price' => 24.90, 'label' => '3 meses', 'discount' => 16],
        6 => ['price' => 44.90, 'label' => '6 meses', 'discount' => 24],
        12 => ['price' => 79.90, 'label' => '1 ano', 'discount' => 33],
    ],

    'limits' => [
        'free' => [
            'projects' => 1,
            'companies' => 1,
            'history_months' => 2,
        ],
        'premium' => [
            'projects' => null, // ilimitado
            'companies' => null,
            'history_months' => null,
        ],
    ],

    'features' => [
        'view_by_day' => 'premium',
        'export_pdf' => 'premium',
        'export_excel' => 'premium',
        'import_csv' => 'premium',
        'multiple_projects' => 'premium',
        'multiple_companies' => 'premium',
        'analytics' => 'premium',
    ],
];
