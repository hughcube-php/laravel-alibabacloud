<?php

return [
    'default' => 'default',
    'clients' => [
        'default' => [
            'AccessKeyID' => md5(random_bytes(100)),
            'AccessKeySecret' => md5(random_bytes(100)),
            'RegionId' => md5(random_bytes(100)),
            'AccountId' => md5(random_bytes(100)),
            'Options' => [
            /** http Options */
            ],
        ],
        'default2' => [
            'AccessKeyID' => md5(random_bytes(100)),
            'AccessKeySecret' => md5(random_bytes(100)),
            'RegionId' => md5(random_bytes(100)),
            'AccountId' => md5(random_bytes(100)),
            'Options' => [
            /** http Options */
            ],
        ],
        'old' => [
            'accessKey' => md5(random_bytes(100)),
            'accessKeySecret' => md5(random_bytes(100)),
            'regionId' => md5(random_bytes(100)),
        ],
    ],
];
