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

    const PROFIT_TOOLS = 'Profit Tools',
        COMPCARE = 'Compcare';

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

    /**
     * Get TMS provider 'Compcare'.
     */
    public static function getCompcare(): self
    {
        return static::where('name', static::COMPCARE)->first();
    }
}
