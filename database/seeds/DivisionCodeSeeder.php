<?php

/**
 * To run:
 * php artisan db:seed --class="DivisionCodeSeeder"
 */



use App\Models\Company;
use App\Models\TMSProvider;
use App\Models\DivisionCode;
use Illuminate\Database\Seeder;

class DivisionCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    /*
     *
     * Raw data supplied by Tom Burke and Heather
     * in a spreadsheet called "Terminal List 2020-5-15(2).xlsx"
     * and attached to JIRA ticket D3-301
     */
    const T_DIVISION_CODE_OVERRIDES = [

        // cushing / profittools
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => Company::CUSHING,  'division_name' => 'Crosstowns'                     , 'division_code' => '2207'                    ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => Company::CUSHING,  'division_name' => 'Crosstowns (OCC)'               , 'division_code' => '2208'                    ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => Company::CUSHING,  'division_name' => 'Orland Park'                    , 'division_code' => '2204'                    ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => Company::CUSHING,  'division_name' => 'Orland Park (OCC)'              , 'division_code' => '2206'                    ],

        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => Company::TCOMPANIES_DEMO,  'division_name' => 'Crosstowns'            , 'division_code' => '2207'                    ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => Company::TCOMPANIES_DEMO,  'division_name' => 'Crosstowns (OCC)'      , 'division_code' => '2208'                    ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => Company::TCOMPANIES_DEMO,  'division_name' => 'Orland Park'           , 'division_code' => '2204'                    ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => Company::TCOMPANIES_DEMO,  'division_name' => 'Orland Park (OCC)'     , 'division_code' => '2206'                    ],

        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => Company::IXT_ONBOARDING,  'division_name' => 'Crosstowns'            , 'division_code' => '2207'                    ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => Company::IXT_ONBOARDING,  'division_name' => 'Crosstowns (OCC)'      , 'division_code' => '2208'                    ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => Company::IXT_ONBOARDING,  'division_name' => 'Orland Park'           , 'division_code' => '2204'                    ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => Company::IXT_ONBOARDING,  'division_name' => 'Orland Park (OCC)'     , 'division_code' => '2206'                    ],

        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => Company::IXT,  'division_name' => 'Crosstowns'                        , 'division_code' => '2207'                    ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => Company::IXT,  'division_name' => 'Crosstowns (OCC)'                  , 'division_code' => '2208'                    ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => Company::IXT,  'division_name' => 'Orland Park'                       , 'division_code' => '2204'                    ],
        ['t_tms_provider_id' => TMSProvider::PROFIT_TOOLS,  't_company_id' => Company::IXT,  'division_name' => 'Orland Park (OCC)'                 , 'division_code' => '2206'                    ],

    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // get ids from database, using lookup by name
        $tcompanies_id = Company::where('name', Company::TCOMPANIES_DEMO)->first()['id'];
        $cushing_id = Company::where('name', Company::CUSHING)->first()['id'];
        $profittools_id = TMSProvider::where('name', TMSProvider::PROFIT_TOOLS)->first()['id'];
        $ixt_onboarding_id = Company::where('name', Company::IXT_ONBOARDING)->first()['id'];
        $ixt_id = Company::where('name', Company::IXT)->first()['id'];


        // for each location-name-override
        foreach (DivisionCodeSeeder::T_DIVISION_CODE_OVERRIDES as $lno) {
            // fix id's
            if ($lno['t_company_id'] == Company::TCOMPANIES_DEMO) {
                $lno['t_company_id'] = $tcompanies_id;
            }
            if ($lno['t_company_id'] == Company::CUSHING) {
                $lno['t_company_id'] = $cushing_id;
            }
            if ($lno['t_company_id'] == Company::IXT_ONBOARDING) {
                $lno['t_company_id'] = $ixt_onboarding_id;
            }
            if ($lno['t_company_id'] == Company::IXT) {
                $lno['t_company_id'] = $ixt_id;
            }
            if ($lno['t_tms_provider_id'] == TMSProvider::PROFIT_TOOLS) {
                $lno['t_tms_provider_id'] = $profittools_id;
            }


            // create a new row if needed, otherwise update. does not insert new rows.
            $override_name = [ 'division_name' => $lno['division_name'] ];
            unset($lno['division_name']); // i.e. move override_name into 2nd parameter, because that value may be updated in future
            $DivisionCodeOverride = DivisionCode::updateOrCreate($lno, $override_name);

            // a little console output
            $msg = "Id {$DivisionCodeOverride['id']}: company={$lno['t_company_id']}, tms_provider={$lno['t_tms_provider_id']}, lno={$lno['division_name']}";
            $this->command->info($msg);
        }
    }
}
