<?php

namespace App\Models\Traits;

use App\Events\AttributeVerified;

trait VerifiesUserSelectedAttributes
{
    public static function bootVerifiesUserSelectedAttributes()
    {
        static::updated(function ($model) {
            collect(static::getAttributesToVerify())
                ->filter(function ($event, $attribute) use ($model) {
                    return  $model->getOriginal($attribute) == false
                        && $model->{$attribute} == true;
                })
                ->each(function ($event, $attribute) use ($model) {
                    if ($event === AttributeVerified::class) {
                        $event::dispatch($model, $attribute);
                        return;
                    }

                    $event::dispatch($model);
                });
        });
    }

    public static function getAttributesToVerify(): array
    {
        return property_exists(static::class, 'verifiableAttributes')
            ? static::$verifiableAttributes
            : [];
    }
}
