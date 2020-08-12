<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Company;
use Illuminate\Support\Arr;
use Faker\Generator as Faker;
use App\Models\OCRRequestStatus;

$factory->define(OCRRequestStatus::class, function (Faker $faker) {
    $requestId = $faker->uuid;

    return [
        'request_id' => $requestId,
        'status_date' => now()->toDateTimeString(),
        'status' => Arr::random(array_keys(OCRRequestStatus::STATUS_MAP)),
        'company_id' => factory(Company::class),
        'status_metadata' => json_encode([]),
    ];
});
