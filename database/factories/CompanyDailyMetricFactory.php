<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Company;
use Faker\Generator as Faker;
use App\Models\CompanyDailyMetric;

$factory->define(CompanyDailyMetric::class, function (Faker $faker) {
    return [
        'metric_date' => $faker->date(),
        't_company_id' => factory(Company::class),
        'requests' => $faker->numberBetween(0, 2000),
        'requests_all_updateprior' => $faker->numberBetween(0, 2000),
        'requests_mixed_updateprior' => $faker->numberBetween(0, 2000),
        'requests_none_updateprior' => $faker->numberBetween(0, 2000),
        'pdf_requests' => $faker->numberBetween(0, 2000),
        'pdf_requests_all_updateprior' => $faker->numberBetween(0, 2000),
        'pdf_requests_mixed_updateprior' => $faker->numberBetween(0, 2000),
        'pdf_requests_none_updateprior' => $faker->numberBetween(0, 2000),
        'pdf_requests_singleorder' => $faker->numberBetween(0, 2000),
        'pdf_requests_singleorder_all_updateprior' => $faker->numberBetween(0, 2000),
        'pdf_requests_singleorder_mixed_updateprior' => $faker->numberBetween(0, 2000),
        'pdf_requests_singleorder_none_updateprior' => $faker->numberBetween(0, 2000),
        'pdf_requests_multiorder' => $faker->numberBetween(0, 2000),
        'pdf_requests_multiorder_all_updateprior' => $faker->numberBetween(0, 2000),
        'pdf_requests_multiorder_mixed_updateprior' => $faker->numberBetween(0, 2000),
        'pdf_requests_multiorder_none_updateprior' => $faker->numberBetween(0, 2000),
        'pdf_requests_multiorder_less_all_updateprior' => $faker->numberBetween(0, 2000),
        'pdf_orders' => $faker->numberBetween(0, 2000),
        'pdf_orders_updateprior' => $faker->numberBetween(0, 2000),
        'pdf_orders_dontupdateprior' => $faker->numberBetween(0, 2000),
        'pdf_orders_less_requests_anyupdateprior' => $faker->numberBetween(0, 2000),
        'datafile_requests' => $faker->numberBetween(0, 2000),
        'datafile_requests_all_updateprior' => $faker->numberBetween(0, 2000),
        'datafile_requests_mixed_updateprior' => $faker->numberBetween(0, 2000),
        'datafile_requests_none_updateprior' => $faker->numberBetween(0, 2000),
        'datafile_orders' => $faker->numberBetween(0, 2000),
        'datafile_orders_updateprior' => $faker->numberBetween(0, 2000),
        'datafile_orders_dontupdateprior' => $faker->numberBetween(0, 2000),
        'datafile_orders_less_requests_anyupdateprior' => $faker->numberBetween(0, 2000),
        'orders_deleted' => $faker->numberBetween(0, 2000),
        'pdf_orders_deleted' => $faker->numberBetween(0, 2000),
        'datafile_orders_deleted' => $faker->numberBetween(0, 2000),
        'requests_rejected' => $faker->numberBetween(0, 2000),
        'pdf_pages_including_deleted' => $faker->numberBetween(0, 2000),
        'tms_shipments' => $faker->numberBetween(0, 2000),
        'requests_deleted' => $faker->numberBetween(0, 2000),
        'pdf_requests_deleted' => $faker->numberBetween(0, 2000),
        'datafile_requests_deleted' => $faker->numberBetween(0, 2000),
        'pdf_orders_including_deleted' => $faker->numberBetween(0, 2000),
        'datafile_orders_including_deleted' => $faker->numberBetween(0, 2000),
        'orders' => $faker->numberBetween(0, 2000),
    ];
});
