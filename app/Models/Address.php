<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @property \Illuminate\Database\Eloquent\Collection $canonicalAddressMatches
 * @property \Illuminate\Database\Eloquent\Collection $canonicalAddresses
 * @property \Illuminate\Database\Eloquent\Collection $companies
 * @property \Illuminate\Database\Eloquent\Collection $companyAddressTmsCodes
 * @property \Illuminate\Database\Eloquent\Collection $contacts
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
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];

    public function canonicalAddressMatches()
    {
        return $this->hasMany(\App\Models\CanonicalAddressMatch::class, 't_address_id');
    }

    public function canonicalAddresses()
    {
        return $this->hasMany(\App\Models\CanonicalAddress::class, 't_address_id');
    }

    public function companies()
    {
        return $this->hasMany(\App\Models\Company::class, 't_address_id');
    }

    public function companyAddressTmsCodes()
    {
        return $this->hasMany(\App\Models\CompanyAddressTmsCode::class, 't_address_id');
    }

    public function contacts()
    {
        return $this->hasMany(\App\Models\Contact::class, 't_address_id');
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
                    'address_line_1' => $data['addr1'] ?? null,
                    'address_line_2' => $data['addr2'] ?? null,
                    'city' => $data['city'] ?? null,
                    'state' => $data['state'] ?? null,
                    'postal_code' => $data['zip'] ?? null,
                    'country' => $data['country'] ?? null,
                    'location_name' => $data['name'] ?? null,
                    'location_phone' => $data['phone'] ?? null,
                    'is_billable' => strtoupper($data['co_allow_billing'] ?? '') == 'T',
                    'is_terminal' => strtoupper($data['terminationlocation'] ?? '') == 'T',
                ];
                break;
        }
    }

    public function isTheSameAs($address, $source = null): bool
    {
        switch ($source) {
            case 'ripcms':
                return $this->address_line_1 == ($address['addr1'] ?? null) &&
                $this->address_line_2 == ($address['addr2'] ?? null) &&
                $this->city == ($address['city'] ?? null) &&
                $this->state == ($address['state'] ?? null) &&
                $this->postal_code == ($address['zip'] ?? null) &&
                $this->country == ($address['country'] ?? null) &&
                $this->location_name == ($address['name'] ?? null) &&
                $this->location_phone == ($address['phone'] ?? null);
        }

        return false;
    }
}
