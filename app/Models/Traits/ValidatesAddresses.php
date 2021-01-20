<?php

namespace App\Models\Traits;

trait ValidatesAddresses
{
    public function isValidated(): bool
    {
        return $this->port_ramp_of_destination_address_verified
            && $this->port_ramp_of_origin_address_verified
            && $this->bill_to_address_verified
            && $this->areOrderAddressEventsVerified();
    }

    protected function areOrderAddressEventsVerified()
    {
        return $this->orderAddressEvents
            ->where('t_address_verified', false)
            ->count() == 0;
    }

    public function notValidatedAddresses(): array
    {
        return collect([
            'port_ramp_of_destination_address_verified',
            'port_ramp_of_origin_address_verified',
            'bill_to_address_verified',
        ])->reject(function ($attribute) {
            return $this->{$attribute} === true;
        })->when(! $this->areOrderAddressEventsVerified(), function ($collection) {
            return $collection->push('order_address_events.*.t_address_verified');
        })->toArray();
    }
}
