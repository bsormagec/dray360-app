<?php

use App\Models\Company;
use App\Contracts\CurrentCompany;

function s3_file_name_from_url(string $url): string
{
    $pieces = collect(preg_split('|\/|', $url));

    if ($pieces->count() < 4) {
        throw new Exception('Invalid S3 url. IT should be like s3://bucket/filepath');
    }

    return $pieces->slice(3)->join('/');
}

function s3_bucket_from_url(string $url): string
{
    $pieces = collect(preg_split('|\/|', $url));

    if ($pieces->count() < 4) {
        throw new Exception('Invalid S3 url. IT should be like s3://bucket/filepath');
    }

    return $pieces->get(2);
}

function currentCompany(?CurrentCompany $company = null): ?Company
{
    if ($company) {
        app()->instance(CurrentCompany::class, $company);
        return $company;
    }

    if (! app()->bound(CurrentCompany::class)) {
        return null;
    }

    return app(CurrentCompany::class);
}

function is_superadmin(?string $guard = null)
{
    return ! auth($guard)->guest() && auth($guard)->user()->isSuperadmin();
}
