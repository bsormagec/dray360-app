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
            'all-orders' => 'e',
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
            'all-companies' => 'v',
            'tms-providers' => 'c,v,e,r',
            'verified-addresses' => 'c,v,e,r',
            'equipment-types' => 'c,v,e,r',
            'addresses' => 'c,v,e,r',
            'company-address-tms-code' => 'c,v,e,r',
            'dictionary-items' => 'c,v,e,r',
            'admin-review' => 'v,e',
            'audit-logs' => 'v',
            'nova' => 'v',
            'object-locks' => 'c,e',
            'supervise' => 'v',
            'pt-images' => 'c',
        ],
        'order-review' => [
            'billing-mapping' => 'e,v',
            'orders' => 'e,v,c,r',
            'all-orders' => 'e',
            'order-address-events' => 'c,v,e,r',
            'order-line-items' => 'c,v,e,r',
            'users' => 'v,i',
            'system-status' => 'f',
            'time-in-status' => 'v',
            'ocr-variants' => 'v',
            'ocr-requests' => 'c,v,e,r',
            'ocr-request-statuses' => 'c,v,e,r',
            'companies' => 'v',
            'all-companies' => 'v',
            'tms-providers' => 'v',
            'verified-addresses' => 'v',
            'equipment-types' => 'v',
            'addresses' => 'v',
            'company-address-tms-code' => 'v',
            'dictionary-items' => 'v,c',
            'admin-review' => 'v,e',
            'audit-logs' => 'v',
            'object-locks' => 'c,e',
            'pt-images' => 'c',
        ],
        'customer-admin' => [
            'orders' => 'e,v,c,r',
            'ocr-requests' => 'c,v,e',
            'tms' => 's',
            'users' => 'v,c,e,r',
            'roles' => 'u',
            'ocr-variants' => 'v',
            'dictionary-items' => 'v,c',
            'audit-logs' => 'v',
            'pt-images' => 'c',
        ],
        'customer-user' => [
            'ocr-requests' => 'c,v',
            'orders' => 'v,c,e',
            'ocr-variants' => 'v',
            'dictionary-items' => 'v,c',
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
