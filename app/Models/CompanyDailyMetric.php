<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyDailyMetric extends Model
{
    use HasFactory;
    use BelongsToCompany;

    public $table = 't_company_daily_metrics';

    protected $dates = ['metric_date'];

    public $fillable = [
        'metric_date',
        't_company_id',
        'requests',
        'requests_all_updateprior',
        'requests_mixed_updateprior',
        'requests_none_updateprior',
        'pdf_requests',
        'pdf_requests_all_updateprior',
        'pdf_requests_mixed_updateprior',
        'pdf_requests_none_updateprior',
        'pdf_requests_singleorder',
        'pdf_requests_singleorder_all_updateprior',
        'pdf_requests_singleorder_mixed_updateprior',
        'pdf_requests_singleorder_none_updateprior',
        'pdf_requests_multiorder',
        'pdf_requests_multiorder_all_updateprior',
        'pdf_requests_multiorder_mixed_updateprior',
        'pdf_requests_multiorder_none_updateprior',
        'pdf_requests_multiorder_less_all_updateprior',
        'pdf_orders',
        'pdf_orders_updateprior',
        'pdf_orders_dontupdateprior',
        'pdf_orders_less_requests_anyupdateprior',
        'datafile_requests',
        'datafile_requests_all_updateprior',
        'datafile_requests_mixed_updateprior',
        'datafile_requests_none_updateprior',
        'datafile_orders',
        'datafile_orders_updateprior',
        'datafile_orders_dontupdateprior',
        'datafile_orders_less_requests_anyupdateprior',
        'orders_deleted',
        'pdf_orders_deleted',
        'datafile_orders_deleted',
        'requests_rejected',
        'pdf_pages_including_deleted',
        'tms_shipments',
        'requests_deleted',
        'pdf_requests_deleted',
        'datafile_requests_deleted',
        'pdf_orders_including_deleted',
        'datafile_orders_including_deleted',
    ];
}
