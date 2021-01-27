<?php

return [
    /**
     * Control if the seeder should create a user per role while seeding the data.
     */
    'create_users' => false,

    /**
     * Control if all the laratrust tables should be truncated before running the seeder.
     */
    'truncate_tables' => false,

    'roles_structure' => [
        'superadmin' => [
            'refs-custom-mapping' => 'e',
            'billing-mapping' => 'e,v',
            'rules-editor' => 'c,v,e,a',
            'orders' => 'e,v,c,r',
            'order-address-events' => 'c,v,e,r',
            'order-line-items' => 'c,v,e,r',
            'tms' => 's,rs',
            'users' => 'v,c,e,r,i',
            'roles' => 'u',
            'system-status' => 'f',
            'time-in-status' => 'v',
            'ocr-variants' => 'c,v,e,r',
            'ocr-requests' => 'c,v,e,r',
            'ocr-request-statuses' => 'c,v,e,r',
            'companies' => 'c,v,e,r',
            'tms-providers' => 'c,v,e,r',
            'verified-addresses' => 'c,v,e,r',
            'equipment-types' => 'c,v,e,r',
            'addresses' => 'c,v,e,r',
            'company-address-tms-code' => 'c,v,e,r',
            'dictionary-items' => 'c,v,e,r',
            'admin-review' => 'v,e'
        ],
        'customer-admin' => [
            'orders' => 'e,v,c,r',
            'ocr-requests' => 'c,v,e',
            'tms' => 's',
            'users' => 'v,c,e,r',
            'roles' => 'u',
            'ocr-variants' => 'v',
            'dictionary-items' => 'v',
        ],
        'customer-user' => [
            'ocr-requests' => 'c,v',
            'orders' => 'v,c,e',
            'ocr-variants' => 'v',
            'dictionary-items' => 'v',
        ],
    ],

    'permissions_map' => [
        'a' => 'assign',
        'c' => 'create',
        'e' => 'edit',
        'f' => 'filter',
        'i' => 'impersonate',
        's' => 'submit',
        'u' => 'update',
        'v' => 'view',
        'r' => 'remove',
        'rs' => 'resubmit',
    ]
];
