<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $name
 */
class TMSProvider extends Model
{
    use SoftDeletes;

    public $table = 't_tms_providers';

    const CREATED_AT = 'created_at',
        UPDATED_AT = 'updated_at',
        PROFIT_TOOLS = 'Profit Tools';

    protected $dates = ['deleted_at'];

    public $fillable = ['name'];

    /**
     * The attributes that should be casted to native types.
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string'
    ];

    /**
     * Validation rules
     */
    public static $rules = [
    ];

    /**
     * Get TMS provider 'Profit Tools'.
     */
    public static function getProfitTools(): self
    {
        return static::where('name', static::PROFIT_TOOLS)->first();
    }
}
