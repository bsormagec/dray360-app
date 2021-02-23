<?php

namespace Tests\Feature\Jobs;

use Tests\TestCase;
use App\Models\TMSProvider;
use Tests\Seeds\CompaniesSeeder;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ImportItgCargoWiseAddress;
use Tests\Seeds\CargoWiseItgAddressesSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ImportItgCargoWiseAddressTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(CompaniesSeeder::class);
        $this->seed(CargoWiseItgAddressesSeeder::class);
        Queue::fake();
    }

    /** @test */
    public function it_inserts_a_new_address_if_it_doesnt_find_an_existing_one()
    {
        $addresses = $this->getBaseAddresses();
        $company = CompaniesSeeder::getTestItg();
        $tmsProvider = TMSProvider::getCargoWise();

        (new ImportItgCargoWiseAddress($addresses[0], $tmsProvider->id, $company))->handle();
        (new ImportItgCargoWiseAddress($addresses[1], $tmsProvider->id, $company))->handle();

        $this->assertDatabaseCount('t_company_address_tms_code', 2);
        $this->assertDatabaseHas('t_addresses', [
            'address_line_1' => $addresses[1]['address_line_1'],
            'address_line_2' => $addresses[1]['address_line_2'],
            'city' => $addresses[1]['city'],
            'state' => $addresses[1]['state'],
            'postal_code' => $addresses[1]['post_code'],
            'country' => null,
            'location_name' => $addresses[1]['org_name'],
            'location_phone' => null,
            'is_terminal' => 0,
            'is_billable' => $addresses[1]['is_billable'],
        ]);
    }

    /** @test */
    public function it_updates_the_already_exiting_address()
    {
        $modifiedAddress = $this->getBaseAddresses()[1];
        $modifiedAddress['org_code'] = $this->getBaseAddresses()[0]['org_code'];
        $company = CompaniesSeeder::getTestItg();
        $tmsProvider = TMSProvider::getCargoWise();

        (new ImportItgCargoWiseAddress($this->getBaseAddresses()[0], $tmsProvider->id, $company))->handle();
        (new ImportItgCargoWiseAddress($modifiedAddress, $tmsProvider->id, $company))->handle();

        $this->assertDatabaseCount('t_company_address_tms_code', 1);
        $this->assertDatabaseHas('t_addresses', [
            'address_line_1' => $modifiedAddress['address_line_1'],
            'address_line_2' => $modifiedAddress['address_line_2'],
            'city' => $modifiedAddress['city'],
            'state' => $modifiedAddress['state'],
            'postal_code' => $modifiedAddress['post_code'],
            'country' => null,
            'location_name' => $modifiedAddress['org_name'],
            'location_phone' => null,
            'is_terminal' => 0,
            'is_billable' => $modifiedAddress['is_billable'],
        ]);
    }

    protected function getBaseAddresses()
    {
        return  [
            [
                "" => "",
                "org_code" => "022CAMFTW",
                "org_name" => "022-CAMPING WORLD DFDC",
                "address_line_1" => "2400 SHORT TRACK CT",
                "address_line_2" => "",
                "city" => "FORT WORTH",
                "state" => "TX",
                "post_code" => "76177",
                "receivable" => "N",
                "payable" => "N",
                "is_billable" => false,
              ], [
                "" => "",
                "org_code" => "0AKW00XNE",
                "org_name" => "THE OAKWOOD GROUP",
                "address_line_1" => "9755 INKSTER RD",
                "address_line_2" => "",
                "city" => "TAYLOR",
                "state" => "MI",
                "post_code" => "48180",
                "receivable" => "Y",
                "payable" => "N",
                "is_billable" => true,
              ]
        ];
    }
}
