<?php

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

