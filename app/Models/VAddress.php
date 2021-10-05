<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VAddress extends Model
{
    use SoftDeletes;

    public $table = 'v_addresses';

    protected $primaryKey = 'address_id';

    const DELETED_AT = 'address_deleted_at';

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
