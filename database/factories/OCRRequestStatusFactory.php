<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Illuminate\Support\Arr;
use Faker\Generator as Faker;
use App\Models\OCRRequestStatus;

$factory->define(OCRRequestStatus::class, function (Faker $faker) {
    $requestId = $faker->uuid;

    return [
        'request_id' => $requestId,
        'status_date' => now()->toDateTimeString(),
        'status' => Arr::random(['intake-started', 'ocr-waiting', 'ocr-completed', 'process-ocr-output-file-complete', 'ocr-post-processing-complete']),
        'status_metadata' => json_encode([]),
    ];
});
