<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @property \Illuminate\Database\Eloquent\Collection $canonicalAddressMatches
 * @property \Illuminate\Database\Eloquent\Collection $canonicalAddresses
 * @property \Illuminate\Database\Eloquent\Collection $companies
 * @property \Illuminate\Database\Eloquent\Collection $companyAddressTmsCodes
 * @property \Illuminate\Database\Eloquent\Collection $orderAddressEvents
 * @property int $id
 * @property float $latitude
 * @property float $longitude
 * @property string $address_line_1
 * @property string $address_line_2
 * @property string $city
 * @property string $county
 * @property string $state
 * @property string $postal_code
 * @property string $country
 * @property string $hours_of_operation
 * @property string $location_name
 * @property string $location_phone
 * @property string $address_concatenated_text
 * @property bool $is_terminal
 * @property bool $is_billable
 * @property bool $is_cc_payor
 * @property bool $is_cc_customer
 * @property bool $is_cc_ssrr
 * @property bool $is_cc_carrier
 * @property bool $is_cc_consignee
 * @property bool $is_cc_driver
 * @property bool $is_cc_shipper
 * @property bool $is_cc_vendor
 */
class Address extends Model
{
    use SoftDeletes;

    public $table = 't_addresses';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'latitude',
        'longitude',
        'address_line_1',
        'address_line_2',
        'city',
        'county',
        'state',
        'postal_code',
        'country',
        'hours_of_operation',
        'location_name',
        'location_phone',
        'is_terminal',
        'is_billable',
        'is_cc_payor',
        'is_cc_customer',
        'is_cc_ssrr',
        'is_cc_carrier',
        'is_cc_consignee',
        'is_cc_driver',
        'is_cc_shipper',
        'is_cc_vendor',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'is_billable' => 'boolean',
        'is_terminal' => 'boolean',
        'is_cc_payor' => 'boolean',
        'is_cc_customer' => 'boolean',
        'is_cc_ssrr' => 'boolean',
        'is_cc_carrier' => 'boolean',
        'is_cc_consignee' => 'boolean',
        'is_cc_driver' => 'boolean',
        'is_cc_shipper' => 'boolean',
        'is_cc_vendor' => 'boolean',
    ];

    public function companies()
    {
        return $this->hasMany(\App\Models\Company::class, 't_address_id');
    }

    public function companyAddressTmsCodes()
    {
        return $this->hasMany(\App\Models\CompanyAddressTmsCode::class, 't_address_id');
    }

    public function orderAddressEvents()
    {
        return $this->hasMany(\App\Models\OrderAddressEvent::class, 't_address_id');
    }

    /**
     * Create a new address in the database from the given structured data.
     * The data will be handled according with the data source.
     */
    public static function createFrom($data, string $dataSource): self
    {
        $finalData = static::transformDataFromTms($data, $dataSource);

        return static::create($finalData);
    }

    /**
     * Update the current address with the given structured data.
     * The data will be handled according with the data source.
     */
    public function updateFrom($data, string $dataSource): bool
    {
        $finalData = static::transformDataFromTms($data, $dataSource);

        return $this->update($finalData);
    }

    /**
     * Transform the data from the tms response to the database structure.
     */
    public static function transformDataFromTms($data, string $dataSource): array
    {
        switch ($dataSource) {
            case 'ripcms':
                return [
                    'address_line_1' => $data['addr1'],
                    'address_line_2' => $data['addr2'],
                    'city' => $data['city'],
                    'state' => $data['state'],
                    'postal_code' => $data['zip'],
                    'country' => $data['country'],
                    'location_name' => $data['name'],
                    'location_phone' => $data['phone'],
                    'is_billable' => strtoupper($data['co_allow_billing']) == 'T',
                    'is_terminal' => strtoupper($data['terminationlocation']) == 'T',
                ];
            case 'compcare':
                $entityTypes = collect(Arr::get($data, 'Entity.EntityTypes'))->pluck('EntityType');
                return [
                    'address_line_1' => $data['AddressLine1'],
                    'address_line_2' => $data['AddressLine2'],
                    'city' => Arr::get($data, 'City.CityName') ?? $data['CityName'],
                    'state' => Arr::get($data, 'State.StateCode'),
                    'postal_code' => Arr::get($data, 'PostalCodeNavigation.PostalCode') ?? $data['PostalCode'],
                    'country' => Arr::get($data, 'Country.CountryCode'),
                    'location_name' => Arr::get($data, 'Entity.EntityName'),
                    'location_phone' => null,
                    'is_billable' => 0,
                    'is_terminal' => 0,
                    'is_cc_payor' => $entityTypes->contains('Payor'),
                    'is_cc_customer' => $entityTypes->contains('Customer'),
                    'is_cc_ssrr' => $entityTypes->contains('SSRR'),
                    'is_cc_carrier' => $entityTypes->contains('Carrier'),
                    'is_cc_consignee' => $entityTypes->contains('Consignee'),
                    'is_cc_driver' => $entityTypes->contains('Driver'),
                    'is_cc_shipper' => $entityTypes->contains('Shipper'),
                    'is_cc_vendor' => $entityTypes->contains('Vendor'),
                ];
            case 'itg-cargowise':
                return [
                    'address_line_1' => $data['address_line_1'],
                    'address_line_2' => $data['address_line_2'],
                    'city' => $data['city'],
                    'state' => $data['state'],
                    'postal_code' => $data['post_code'],
                    'country' => null,
                    'location_name' => $data['org_name'],
                    'location_phone' => null,
                    'is_billable' => $data['is_billable'] ?? 0,
                    'is_terminal' => 0,
                ];
        }
    }

    public function isTheSameAs($address, $source = null): bool
    {
        switch ($source) {
            case 'ripcms':
                return $this->address_line_1 == $address['addr1'] &&
                $this->address_line_2 == $address['addr2'] &&
                $this->city == $address['city'] &&
                $this->state == $address['state'] &&
                $this->postal_code == $address['zip'] &&
                $this->country == $address['country'] &&
                $this->location_name == $address['name'] &&
                $this->location_phone == $address['phone'] &&
                $this->is_billable == (strtoupper($address['co_allow_billing']) == 'T') &&
                $this->is_terminal == (strtoupper($address['co_allow_billing']) == 'T');
            case 'compcare':
                $entityTypes = collect(Arr::get($address, 'Entity.EntityTypes'))->pluck('EntityType');
                return $this->address_line_1 == $address['AddressLine1'] &&
                $this->address_line_2 == $address['AddressLine2'] &&
                $this->city == (Arr::get($address, 'City.CityName') ?? $address['CityName']) &&
                $this->state == Arr::get($address, 'State.StateCode') &&
                $this->postal_code == (Arr::get($address, 'PostalCodeNavigation.PostalCode') ?? $address['PostalCode']) &&
                $this->country == Arr::get($address, 'Country.CountryCode') &&
                $this->location_name == Arr::get($address, 'Entity.EntityName') &&
                $this->location_phone == null &&
                $this->is_billable == 0 &&
                $this->is_terminal == 0 &&
                $this->is_cc_payor == $entityTypes->contains('Payor') &&
                $this->is_cc_customer == $entityTypes->contains('Customer') &&
                $this->is_cc_ssrr == $entityTypes->contains('SSRR') &&
                $this->is_cc_carrier == $entityTypes->contains('Carrier') &&
                $this->is_cc_consignee == $entityTypes->contains('Consignee') &&
                $this->is_cc_driver == $entityTypes->contains('Driver') &&
                $this->is_cc_shipper == $entityTypes->contains('Shipper') &&
                $this->is_cc_vendor == $entityTypes->contains('Vendor');
            case 'itg-cargowise':
                return $this->address_line_1 == $address['address_line_1'] &&
                $this->address_line_2 == $address['address_line_2'] &&
                $this->city == $address['city'] &&
                $this->state == $address['state'] &&
                $this->postal_code == $address['post_code'] &&
                $this->country == null &&
                $this->location_name == $address['org_name'] &&
                $this->location_phone == null &&
                $this->is_billable == $address['is_billable'] ?? 0 &&
                $this->is_terminal == 0;
        }

        return false;
    }
}
