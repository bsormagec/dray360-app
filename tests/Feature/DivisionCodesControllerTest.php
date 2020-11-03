<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Company;
use App\Models\TMSProvider;
use App\Models\DivisionCode;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DivisionCodesControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_return_all_the_division_codes()
    {
        $this->loginAdmin();
        $company = factory(Company::class)->create();
        $tmsProvider = factory(TMSProvider::class)->create();
        $divisionCodes = factory(DivisionCode::class, 5)->create([
            't_company_id' => $company->id,
            't_tms_provider_id' => $tmsProvider->id
        ]);
        $testDivisionCodes = $divisionCodes->first()->refresh();


        $this->getJson(route('division-names.show', [
            'company' => $company->id,
            'tmsProvider' => $tmsProvider->id,
            'division_name' => $testDivisionCodes->division_name,
            'division_code' => $testDivisionCodes->division_code
        ]))
        ->assertStatus(Response::HTTP_OK);
    }
}
