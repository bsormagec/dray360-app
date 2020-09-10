<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Company;
use App\Models\OCRVariant;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AccesorialControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginAdmin();
    }

    /** @test */
    public function it_should__update_the__accesorialmapping_fields()
    {
        $company = factory(Company::class)->create();
        $mapping = ['Ben' => 37, 'Joe' => 43, 'Peter' => 35];
        $variant = factory(OCRVariant::class)->create();

        //companies/{company}/variants/{variant}
        $this->putJson(route('company-variants-accessorials.put', [$company->id, $variant->id]), ['billing-mapping' => $mapping])
            ->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('t_company_ocrvariant_accessorial_mappings', [
            'mapping->Ben' => $mapping['Ben'],
            'mapping->Joe' => $mapping['Joe'],
            'mapping->Peter' => $mapping['Peter'],
            't_company_id' => $company->id,
            't_ocrvariant_id' => $variant->id,
        ]);
    }

    /** @test */
    public function it_should__retrieve_the__accesorialmapping_fields_by_id()
    {
        $company = factory(Company::class)->create();
        $variant = factory(OCRVariant::class)->create();
        $mappingObject = [
            'mapping' => ['Ben' => 37, 'Joe' => 43, 'Peter' => 35]
        ];

        $company->variantsAccessorials()->syncWithoutDetaching([$variant->id => $mappingObject]);

        $this->getJson(route('company-variants-accessorials.show', [$company->id, $variant->id]))
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['mapping' => $mappingObject['mapping']]);
    }
}
