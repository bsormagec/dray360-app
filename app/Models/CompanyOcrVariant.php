<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class CompanyOcrVariant extends Model
{
    use BelongsToCompany;

    public $table = 't_company_ocrvariant';

    public $fillable = [
        't_company_id',
        't_ocrvariant_id',
        't_fieldmap_id',
    ];

    public function ocrVariant()
    {
        return $this->belongsTo(OCRVariant::class, 't_ocrvariant_id');
    }

    public function fieldMap()
    {
        return $this->belongsTo(FieldMap::class, 't_fieldmap_id');
    }
}
