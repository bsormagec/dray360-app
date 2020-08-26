<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use SoftDeletes;

    const DEFAULT_ID = 1;

    public $table = 't_tenants';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'name',
        'configuration',
    ];

    /**
     * The attributes that should be casted to native types.
     */
    protected $casts = [
        'name' => 'string',
        'configuration' => 'json'
    ];

    /**
     * Validation rules
     */
    public static $rules = [
        'name' => 'sometimes|string',
        'configuration' => 'sometimes|array',
    ];

    public function domains()
    {
        return $this->hasMany(Domain::class, 't_tenant_id');
    }

    public static function getDefaultTenant(): self
    {
        return static::findOrFail(static::DEFAULT_ID);
    }
}
