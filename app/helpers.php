<?php

use App\Models\Company;

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

function currentCompany(?Company $company = null): ?Company
{
    if ($company) {
        app('tenancy')->setCurrentCompany($company);
        return $company;
    }

    return app('tenancy')->getCurrentCompany();
}

function is_superadmin(?string $guard = null)
{
    return ! auth($guard)->guest() && auth($guard)->user()->isSuperadmin();
}
