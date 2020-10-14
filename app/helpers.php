<?php

use App\Models\Tenant;
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
        app('company_manager')->setCompany($company);
        return $company;
    }

    return app('company_manager')->getCompany();
}

function tenant(?Tenant $tenant = null)
{
    if ($tenant) {
        app('tenancy')->setTenant($tenant);
        return $tenant;
    }

    return app('tenancy')->getTenant();
}

function is_superadmin(?string $guard = null)
{
    return ! auth($guard)->guest() && auth($guard)->user()->isSuperadmin();
}

function request_is_from_nova()
{
    return request()->is(['nova/*', 'nova-api/*']);
}
