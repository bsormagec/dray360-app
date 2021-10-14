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
            'auto-lock-processing' => 'c',
            'auto-lock-not-processing' => 'c',
            'supervise' => 'v',
            'pt-images' => 'c',
            'field-maps' => 'c,v,r',
            'company-field-maps' => 'c,r',
            'tms-field-maps' => 'c,r',
            'variant-field-maps' => 'c,r',
            'metrics' => 'v',
            'feedbacks' => 'c,v',
        ],
        'ops-admin' => [
            'refs-custom-mapping' => 'e',
            'rules-editor' => 'v',
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
            'auto-lock-processing' => 'c',
            'auto-lock-not-processing' => 'c',
            'supervise' => 'v',
            'pt-images' => 'c',
            'field-maps' => 'v',
            'company-field-maps' => 'c,r',
            'metrics' => 'v',
            'feedbacks' => 'c,v',
        ],
        'order-review' => [
            'rules-editor' => 'v',
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
            'auto-lock-processing' => 'c',
            'supervise' => 'v',
            'pt-images' => 'c',
            'feedbacks' => 'c,v',
        ],
        'customer-admin' => [
            'orders' => 'e,v,c,r',
            'all-orders' => 'e',
            'ocr-requests' => 'c,v,e',
            'tms' => 's',
            'users' => 'v,c,e,r',
            'roles' => 'u',
            'ocr-variants' => 'v',
            'dictionary-items' => 'v,c',
            'audit-logs' => 'v',
            'pt-images' => 'c',
            'object-locks' => 'c,e',
            'auto-lock-not-processing' => 'c',
            'feedbacks' => 'c',
        ],
        'customer-user' => [
            'orders' => 'v,c,e',
            'all-orders' => 'e',
            'ocr-requests' => 'c,v,e',
            'tms' => 's',
            'ocr-variants' => 'v',
            'dictionary-items' => 'v,c',
            'audit-logs' => 'v',
            'pt-images' => 'c',
            'object-locks' => 'c,e',
            'auto-lock-not-processing' => 'c',
            'feedbacks' => 'c',
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
