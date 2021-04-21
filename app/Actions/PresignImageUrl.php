<?php

namespace App\Actions;

use Illuminate\Support\Facades\Storage;

class PresignImageUrl
{
    const MINUTES_URI_REMAINS_VALID = 15;

    public function __invoke(string $bucket, string $path, array $options = []): string
    {
        $s3Config = config('filesystems.disks.s3-base') + ['bucket' => $bucket];

        $storage = Storage::createS3Driver($s3Config);
        $urlExpiryTime = now()->addMinutes(self::MINUTES_URI_REMAINS_VALID);

        return $storage->temporaryUrl(
            $path,
            $urlExpiryTime,
            $options
        );
    }
}
