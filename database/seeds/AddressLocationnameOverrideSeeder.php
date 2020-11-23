<?php

/**
 * To run:
 * php artisan db:seed --class="AddressLocationnameOverrideSeeder"
 */



use App\Models\Company;
use App\Models\TMSProvider;
use Illuminate\Database\Seeder;
use App\Models\AddressLocationnameOverride;

class AddressLocationnameOverrideSeeder extends Seeder
{
    const
        CUSHING = 'Cushing',
        TCOMPANIES_DEMO = 'TCompaniesDemo';

    /*
     *
     * Raw data supplied by Tom Burke and Heather
     * in a spreadsheet called "Terminal List 2020-5-15(2).xlsx"
     * and attached to JIRA ticket D3-301
     */
    const T_ADDRESS_LOCATION_NAME_OVERRIDES = [

        // cushing / profittools
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::CUSHING,  'location_name' => 'ATSF   Z 1'            , 'override_name' => 'BNSF Chicago Corwith'                    ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::CUSHING,  'location_name' => 'ATSW   Z 4'            , 'override_name' => 'BNSF Chicago Willow Springs'             ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::CUSHING,  'location_name' => 'BNEL'                  , 'override_name' => 'BNSF Chicago Logistics Park'             ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::CUSHING,  'location_name' => 'BNOG   Z 1'            , 'override_name' => 'BNSF Chicago Cicero'                     ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::CUSHING,  'location_name' => 'CN Z3'                 , 'override_name' => 'BNSF Chicago Harvey'                     ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::CUSHING,  'location_name' => 'CP (EAST)   Z 2 '      , 'override_name' => 'BNSF Chicago Schiller Park'              ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::CUSHING,  'location_name' => 'CP BENSENVILLE   Z 2'  , 'override_name' => 'BNSF Chicago Bensenville'                ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::CUSHING,  'location_name' => 'CSX   Z 1'             , 'override_name' => 'BNSF Chicago Bedford Park'               ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::CUSHING,  'location_name' => 'CSX59TH   Z 1'         , 'override_name' => 'BNSF Chicago 59th Street'                ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::CUSHING,  'location_name' => 'FOX RUN'               , 'override_name' => 'Fox Run Terminal'                        ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::CUSHING,  'location_name' => 'IOWA INTERSTATE'       , 'override_name' => 'IAIS Blue Island'                        ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::CUSHING,  'location_name' => 'NS47TH   Z 1'          , 'override_name' => 'NS Chicago 47th Street'                  ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::CUSHING,  'location_name' => 'NS63RD Z1'             , 'override_name' => 'NS Chicago 63rd St. Englewood'           ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::CUSHING,  'location_name' => 'NS79TH'                , 'override_name' => 'NS Chicago Landers'                      ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::CUSHING,  'location_name' => 'NSCA   Z 3'            , 'override_name' => 'NS Chicago Calumet'                      ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::CUSHING,  'location_name' => 'UP CANAL    Z 1'       , 'override_name' => 'UP Chicago Global I 436 25TH PLACE'      ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::CUSHING,  'location_name' => 'UPG1   Z 1'            , 'override_name' => 'UP Chicago Global I 1425 WESTERN'        ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::CUSHING,  'location_name' => 'UPG2   Z 2'            , 'override_name' => 'UP Chicago Global II 301 WEST LAKE'      ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::CUSHING,  'location_name' => 'UPG4   Z 5'            , 'override_name' => 'UP Joliet Intermodal Terminal Global IV' ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::CUSHING,  'location_name' => 'YARD CENTER   Z3'      , 'override_name' => 'UP Chicago Yard Center'                  ],

        // tcompanies / profittools
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::TCOMPANIES_DEMO,  'location_name' => 'ATSF   Z 1'            , 'override_name' => 'BNSF Chicago Corwith'                    ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::TCOMPANIES_DEMO,  'location_name' => 'ATSW   Z 4'            , 'override_name' => 'BNSF Chicago Willow Springs'             ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::TCOMPANIES_DEMO,  'location_name' => 'BNEL'                  , 'override_name' => 'BNSF Chicago Logistics Park'             ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::TCOMPANIES_DEMO,  'location_name' => 'BNOG   Z 1'            , 'override_name' => 'BNSF Chicago Cicero'                     ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::TCOMPANIES_DEMO,  'location_name' => 'CN Z3'                 , 'override_name' => 'BNSF Chicago Harvey'                     ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::TCOMPANIES_DEMO,  'location_name' => 'CP (EAST)   Z 2 '      , 'override_name' => 'BNSF Chicago Schiller Park'              ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::TCOMPANIES_DEMO,  'location_name' => 'CP BENSENVILLE   Z 2'  , 'override_name' => 'BNSF Chicago Bensenville'                ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::TCOMPANIES_DEMO,  'location_name' => 'CSX   Z 1'             , 'override_name' => 'BNSF Chicago Bedford Park'               ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::TCOMPANIES_DEMO,  'location_name' => 'CSX59TH   Z 1'         , 'override_name' => 'BNSF Chicago 59th Street'                ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::TCOMPANIES_DEMO,  'location_name' => 'FOX RUN'               , 'override_name' => 'Fox Run Terminal'                        ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::TCOMPANIES_DEMO,  'location_name' => 'IOWA INTERSTATE'       , 'override_name' => 'IAIS Blue Island'                        ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::TCOMPANIES_DEMO,  'location_name' => 'NS47TH   Z 1'          , 'override_name' => 'NS Chicago 47th Street'                  ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::TCOMPANIES_DEMO,  'location_name' => 'NS63RD Z1'             , 'override_name' => 'NS Chicago 63rd St. Englewood'           ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::TCOMPANIES_DEMO,  'location_name' => 'NS79TH'                , 'override_name' => 'NS Chicago Landers'                      ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::TCOMPANIES_DEMO,  'location_name' => 'NSCA   Z 3'            , 'override_name' => 'NS Chicago Calumet'                      ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::TCOMPANIES_DEMO,  'location_name' => 'UP CANAL    Z 1'       , 'override_name' => 'UP Chicago Global I 436 25TH PLACE'      ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::TCOMPANIES_DEMO,  'location_name' => 'UPG1   Z 1'            , 'override_name' => 'UP Chicago Global I 1425 WESTERN'        ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::TCOMPANIES_DEMO,  'location_name' => 'UPG2   Z 2'            , 'override_name' => 'UP Chicago Global II 301 WEST LAKE'      ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::TCOMPANIES_DEMO,  'location_name' => 'UPG4   Z 5'            , 'override_name' => 'UP Joliet Intermodal Terminal Global IV' ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => self::TCOMPANIES_DEMO,  'location_name' => 'YARD CENTER   Z3'      , 'override_name' => 'UP Chicago Yard Center'                  ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // get ids from database, using lookup by name
        $tcompanies_id = Company::where('name', self::TCOMPANIES_DEMO)->first()['id'];
        $cushing_id = Company::where('name', self::CUSHING)->first()['id'];
        $profittools_id = TMSProvider::where('name', TMSProvider::PROFIT_TOOLS)->first()['id'];

        // for each location-name-override
        foreach (AddressLocationnameOverrideSeeder::T_ADDRESS_LOCATION_NAME_OVERRIDES as $lno) {
            // fix id's
            if ($lno['t_company_id'] == self::TCOMPANIES_DEMO) {
                $lno['t_company_id'] = $tcompanies_id;
            }
            if ($lno['t_company_id'] == self::CUSHING) {
                $lno['t_company_id'] = $cushing_id;
            }
            if ($lno['t_tms_provider_id'] == TMSProvider::PROFIT_TOOLS) {
                $lno['t_tms_provider_id'] = $profittools_id;
            }

            // create a new row if needed, otherwise update. does not insert new rows.
            $override_name = [ 'override_name' => $lno['override_name'] ];
            unset($lno['override_name']); // i.e. move override_name into 2nd parameter, because that value may be updated in future
            $addressLocationNameOverride = AddressLocationnameOverride::updateOrCreate($lno, $override_name);

            // a little console output
            $msg = "Id {$addressLocationNameOverride['id']}: company={$lno['t_company_id']}, tms_provider={$lno['t_tms_provider_id']}, lno={$lno['location_name']}";
            $this->command->info($msg);
        }
    }
}
