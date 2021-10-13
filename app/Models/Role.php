<?php

namespace App\Models;

use Laratrust\Models\LaratrustRole;

class Role extends LaratrustRole
{
    const SUPERADMIN = 'superadmin',
        OPS_ADMIN = 'ops-admin',
        ORDER_REVIEW = 'order-review',
        CUSTOMER_ADMIN = 'customer-admin',
        CUSTOMER_USER = 'customer-user';

    public $guarded = [];
}
