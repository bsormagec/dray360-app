<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \App\Models\Tenant $tenant
 * @property integer $t_tenant_id
 * @property string $description
 * @property string $hostname
 */
class Domain extends Model
{
    use SoftDeletes;

    public $table = 't_domains';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'description',
        'hostname',
        't_tenant_id',
    ];

    /**
     * The attributes that should be casted to native types.
     */
    protected $casts = [
        'description' => 'string',
        'hostname' => 'string'
    ];

    /**
     * Validation rules
     */
    public static $rules = [
        'description' => 'sometimes|string',
        'hostname' => 'sometimes|string',
        't_tenant_id' => 'sometimes|exists:t_tenants,id',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 't_tenant_id');
    }

    public function companies()
    {
        return $this->hasMany(Company::class, 't_domain_id');
    }
}
