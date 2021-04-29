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
        COMPCARE = 'Compcare',
        CARGOWISE = 'CargoWise';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'name',
        't_fieldmap_id'
    ];

    public function fieldMap()
    {
        return $this->belongsTo(FieldMap::class, 't_fieldmap_id');
    }

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

    /**
     * Get TMS provider 'CargoWise'.
     */
    public static function getCargoWise(): self
    {
        return static::where('name', static::CARGOWISE)->first();
    }
}
