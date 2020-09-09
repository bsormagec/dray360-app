<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property integer $t_company_id
 * @property integer $t_variant_id
 * @property json $mapping
 *
 */
class AccesorialMappingPivot extends Pivot
{
    protected $casts = [
        'mapping' => 'json'
    ];
}
