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

function get_memory_usage_mb()
{
    return round(memory_get_usage(true) / 1048576, 2);
}

function array_diff_assoc_recursive($array1, $array2)
{
    $difference = [];
    foreach ($array1 as $key => $value) {
        if (is_array($value)) {
            if (! isset($array2[$key]) || ! is_array($array2[$key])) {
                $difference[$key] = $value;
            } else {
                $new_diff = array_diff_assoc_recursive($value, $array2[$key]);
                if (! empty($new_diff)) {
                    $difference[$key] = $new_diff;
                }
            }
        } elseif (! array_key_exists($key, $array2) || $array2[$key] !== $value) {
            $difference[$key] = $value;
        }
    }
    return $difference;
}
