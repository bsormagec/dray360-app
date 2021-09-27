<?php

namespace Tests\Seeds;

use App\Models\Address;
use App\Models\TMSProvider;
use Illuminate\Database\Seeder;
use App\Models\CompanyAddressTMSCode;

class CompcareTradelinkAddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = CompaniesSeeder::getTestTradelink();
        $tmsProvider = TMSProvider::getCompcare();

        $baseAddress = self::getBaseAddresses()[0];

        $address = Address::create([
            'address_line_1' => $baseAddress['AddressLine1'],
            'address_line_2' => $baseAddress['AddressLine2'],
            'city' => $baseAddress['City']['CityName'],
            'state' => $baseAddress['State']['StateCode'],
            'postal_code' => $baseAddress['PostalCode'],
            'country' => $baseAddress['Country']['CountryCode'],
            'location_name' => "({$baseAddress['ExternalSystemAddressId']}) ".$baseAddress['Entity']['EntityName'],
            'location_phone' => null,
            'is_terminal' => 0,
            'is_billable' => 0,
            'is_cc_payor' => 0,
            'is_cc_customer' => 0,
            'is_cc_ssrr' => 0,
            'is_cc_carrier' => 0,
            'is_cc_consignee' => 1,
            'is_cc_driver' => 0,
            'is_cc_shipper' => 1,
            'is_cc_vendor' => 0,
        ]);

        factory(CompanyAddressTMSCode::class)->create([
            't_address_id' => $address->id,
            't_company_id' => $company->id,
            't_tms_provider_id' => $tmsProvider->id,
            'company_address_tms_code' => $baseAddress['EntityId'],
        ]);
    }

    public static function getBaseAddresses(): array
    {
        return [
            [
                'AddressId' => 127637,
                'OrganizationId' => 6,
                'EntityId' => 1,
                'LocationTypeId' => 1,
                'CityId' => null,
                'StateId' => 5,
                'CountyId' => null,
                'PostalCodeId' => 19020,
                'CountryId' => 1,
                'ExternalSystemAddressId' => 'ROS11440CA',
                'AddressLine1' => '11440 SHELDON STREET SUN VALLEY, CA',
                'AddressLine2' => '',
                'CityName' => 'SUN VALLEY',
                'PostalCode' => '91352',
                'IsDefault' => false,
                'InsertedUserId' => 0,
                'InsertedDate' => '2021-05-07T16:57:00',
                'UpdatedUserId' => null,
                'UpdatedDate' => null,
                'City' => [
                    'CityName' => 'SUN VALLEY',
                ],
                'Country' => [
                    'CountryId' => 1,
                    'CountryCode' => 'USA',
                    'Country' => 'United States',
                    'InsertedUserId' => 1,
                    'InsertedDate' => '2020-07-01T22:10:00',
                    'UpdatedUserId' => null,
                    'UpdatedDate' => null
                ],
                'County' => null,
                'Entity' => [
                    'EntityId' => 1,
                    'OrganizationId' => 6,
                    'InternalTmsid' => 'ROS11440CA',
                    'ExternalSystemEntityId' => 'ROS11440CA',
                    'EntityName' => 'ROSEBRAND WEST',
                    'InsertedUserId' => 0,
                    'InsertedDate' => '2021-05-07T16:41:00',
                    'UpdatedUserId' => null,
                    'UpdatedDate' => null,
                    'EntityTypes' => [
                        [
                            'EntityTypeId' => 3,
                            'EntityType' => 'Shipper',
                            'EntityTypeDescription' => null,
                            'InsertedUserId' => 0,
                            'InsertedDate' => '2020-01-01T00:00:00',
                            'UpdatedUserId' => null,
                            'UpdatedDate' => null
                        ],
                        [
                            'EntityTypeId' => 4,
                            'EntityType' => 'Consignee',
                            'EntityTypeDescription' => null,
                            'InsertedUserId' => 0,
                            'InsertedDate' => '2020-01-01T00:00:00',
                            'UpdatedUserId' => null,
                            'UpdatedDate' => null
                        ]
                    ]
                ],
                'LocationType' => [
                    'LocationTypeId' => 1,
                    'LocationTypeCode' => 'P',
                    'LocationType' => 'Primary (Mailing Address)',
                    'InsertedUserId' => 0,
                    'InsertedDate' => '2020-10-07T19:02:00',
                    'UpdatedUserId' => null,
                    'UpdatedDate' => null
                ],
                'PostalCodeNavigation' => [
                    'PostalCodeId' => 19020,
                    'CountyId' => null,
                    'StateId' => 5,
                    'PostalCode' => '91352',
                    'InsertedUserId' => 1,
                    'InsertedDate' => '2020-07-01T22:11:00',
                    'UpdatedUserId' => null,
                    'UpdatedDate' => null
                ],
                'State' => [
                    'StateId' => 5,
                    'RegionId' => 4,
                    'RegionDivisionId' => 9,
                    'CountryId' => 1,
                    'StateCode' => 'CA',
                    'State' => 'California',
                    'InsertedUserId' => 1,
                    'InsertedDate' => '2020-07-01T22:15:00',
                    'UpdatedUserId' => null,
                    'UpdatedDate' => null,
                    'Region' => [
                        'RegionId' => 4,
                        'RegionName' => 'West',
                        'InsertedUserId' => 1,
                        'InsertedDate' => '2020-07-01T22:12:00',
                        'UpdatedUserId' => null,
                        'UpdatedDate' => null
                    ],
                    'RegionDivision' => [
                        'RegionDivisionId' => 9,
                        'RegionId' => 4,
                        'RegionDivisionName' => 'Pacific',
                        'InsertedUserId' => 1,
                        'InsertedDate' => '2020-07-01T22:12:00',
                        'UpdatedUserId' => null,
                        'UpdatedDate' => null
                    ]
                ]
            ],
            [
                'AddressId' => 127635,
                'OrganizationId' => 6,
                'EntityId' => 2,
                'LocationTypeId' => 1,
                'CityId' => 2357,
                'StateId' => 5,
                'CountyId' => 2515,
                'PostalCodeId' => 19113,
                'CountryId' => 1,
                'ExternalSystemAddressId' => 'RID437',
                'AddressLine1' => '4377 N BALDWIN AVE',
                'AddressLine2' => '',
                'CityName' => 'EL MONTE',
                'PostalCode' => '91731',
                'IsDefault' => false,
                'InsertedUserId' => 0,
                'InsertedDate' => '2021-05-07T16:56:00',
                'UpdatedUserId' => null,
                'UpdatedDate' => null,
                'City' => [
                    'CityId' => 2357,
                    'CountyId' => 2515,
                    'StateId' => 5,
                    'CityName' => 'El Monte',
                    'Population' => 116109,
                    'Households' => 29550,
                    'MedianIncome' => 43504.00,
                    'Latitude' => 34.068620,
                    'Longitude' => -118.027570,
                    'InsertedUserId' => 1,
                    'InsertedDate' => '2020-07-01T22:08:00',
                    'UpdatedUserId' => null,
                    'UpdatedDate' => null
                ],
                'Country' => [
                    'CountryId' => 1,
                    'CountryCode' => 'USA',
                    'Country' => 'United States',
                    'InsertedUserId' => 1,
                    'InsertedDate' => '2020-07-01T22:10:00',
                    'UpdatedUserId' => null,
                    'UpdatedDate' => null
                ],
                'County' => [
                    'CountyId' => 2515,
                    'StateId' => 5,
                    'CountyName' => 'Los Angeles County',
                    'InsertedUserId' => 1,
                    'InsertedDate' => '2020-07-01T22:09:00',
                    'UpdatedUserId' => null,
                    'UpdatedDate' => null
                ],
                'Entity' => [
                    'EntityId' => 2,
                    'OrganizationId' => 6,
                    'InternalTmsid' => 'RID437',
                    'ExternalSystemEntityId' => 'RID437',
                    'EntityName' => 'RIDER EXPRESS',
                    'InsertedUserId' => 0,
                    'InsertedDate' => '2021-05-07T16:41:00',
                    'UpdatedUserId' => null,
                    'UpdatedDate' => null,
                    'EntityTypes' => [
                        [
                            'EntityTypeId' => 3,
                            'EntityType' => 'Shipper',
                            'EntityTypeDescription' => null,
                            'InsertedUserId' => 0,
                            'InsertedDate' => '2020-01-01T00:00:00',
                            'UpdatedUserId' => null,
                            'UpdatedDate' => null
                        ],
                        [
                            'EntityTypeId' => 4,
                            'EntityType' => 'Consignee',
                            'EntityTypeDescription' => null,
                            'InsertedUserId' => 0,
                            'InsertedDate' => '2020-01-01T00:00:00',
                            'UpdatedUserId' => null,
                            'UpdatedDate' => null
                        ]
                    ]
                ],
                'LocationType' => [
                    'LocationTypeId' => 1,
                    'LocationTypeCode' => 'P',
                    'LocationType' => 'Primary (Mailing Address)',
                    'InsertedUserId' => 0,
                    'InsertedDate' => '2020-10-07T19:02:00',
                    'UpdatedUserId' => null,
                    'UpdatedDate' => null
                ],
                'PostalCodeNavigation' => [
                    'PostalCodeId' => 19113,
                    'CountyId' => null,
                    'StateId' => 5,
                    'PostalCode' => '91731',
                    'InsertedUserId' => 1,
                    'InsertedDate' => '2020-07-01T22:11:00',
                    'UpdatedUserId' => null,
                    'UpdatedDate' => null
                ],
                'State' => [
                    'StateId' => 5,
                    'RegionId' => 4,
                    'RegionDivisionId' => 9,
                    'CountryId' => 1,
                    'StateCode' => 'CA',
                    'State' => 'California',
                    'InsertedUserId' => 1,
                    'InsertedDate' => '2020-07-01T22:15:00',
                    'UpdatedUserId' => null,
                    'UpdatedDate' => null,
                    'Region' => [
                        'RegionId' => 4,
                        'RegionName' => 'West',
                        'InsertedUserId' => 1,
                        'InsertedDate' => '2020-07-01T22:12:00',
                        'UpdatedUserId' => null,
                        'UpdatedDate' => null
                    ],
                    'RegionDivision' => [
                        'RegionDivisionId' => 9,
                        'RegionId' => 4,
                        'RegionDivisionName' => 'Pacific',
                        'InsertedUserId' => 1,
                        'InsertedDate' => '2020-07-01T22:12:00',
                        'UpdatedUserId' => null,
                        'UpdatedDate' => null
                    ]
                ]
            ],
        ];
    }
}
