<?php

use Illuminate\Support\Str;

return [
    'default' => 'default',
    'clients' => [
        'default' => [
            'AccessKeyID' => md5(Str::random(100)),
            'AccessKeySecret' => md5(Str::random(100)),
            'RegionId' => md5(Str::random(100)),
            'AccountId' => md5(Str::random(100)),
            'Options' => [
                /** http Options */
            ],
        ],
        'default2' => [
            'AccessKeyID' => md5((100)),
            'AccessKeySecret' => md5(Str::random(100)),
            'RegionId' => md5(Str::random(100)),
            'AccountId' => md5(Str::random(100)),
            'Options' => [
                /** http Options */
            ],
        ]
    ],
];
