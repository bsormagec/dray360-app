<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Company;
use App\Models\TMSProvider;
use Illuminate\Support\Str;
use App\Models\EquipmentType;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EquipmentTypesControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_return_all_the_equipment_types_and_allow_to_filter_by_display()
    {
        $this->loginAdmin();
        $company = factory(Company::class)->create();
        $tmsProvider = factory(TMSProvider::class)->create();
        $equipmentTypes = factory(EquipmentType::class, 5)->create([
            't_company_id' => $company->id,
            't_tms_provider_id' => $tmsProvider->id,
        ]);
        $testEquipmentType = $equipmentTypes->first()->refresh();

        $queryPart = Str::of($testEquipmentType->equipment_display)
            ->before(' ')
            ->before(',')
            ->__toString();
        $equipmentCount = EquipmentType::where('equipment_display', 'like', "%{$queryPart}%")->count();

        $this->getJson(route('equipment-types.show', [
            'company' => $company->id,
            'tmsProvider' => $tmsProvider->id,
            'filter[query]' => Str::of($testEquipmentType->equipment_display)
                ->before(' ')
                ->before(',')
                ->__toString()
        ]))
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonCount($equipmentCount, 'data');
    }
}
