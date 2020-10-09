<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Crypt;

trait EncryptsAttributes
{
    /**
     * @uses \Illuminate\Database\Eloquent\Concerns\HasAttributes::setAttribute
     */
    public function setAttribute($key, $value)
    {
        if (! in_array($key, $this->getEncryptableAttributes())) {
            return parent::setAttribute($key, $value);
        }

        $this->attributes[$key] = Crypt::encryptString($value);

        return $this;
    }

    /**
     * @uses \Illuminate\Database\Eloquent\Concerns\HasAttributes::getAttribute
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (! in_array($key, $this->getEncryptableAttributes()) || $value == null) {
            return $value;
        }

        return Crypt::decryptString($value);
    }

    protected function getEncryptableAttributes(): array
    {
        return property_exists($this, 'encryptable')
            ? $this->encryptable
            : [];
    }
}
