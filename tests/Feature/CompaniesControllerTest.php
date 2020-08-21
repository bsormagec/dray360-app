<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Company;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CompaniesControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginAdmin();
    }

    /** @test */
    public function it_should_retrieve_all_the_variants_paginated()
    {
        factory(Company::class, 10)->create();

        $this->getJson(route('companies.index'))
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJsonCount(10, 'data');
    }

    /** @test */
    public function it_should__update_the__company_fields()
    {
        $company = factory(Company::class)->create();
        $mapping = ['Ben' => 37, 'Joe' => 43, 'Peter' => 35];
        $company->refs_custom_mapping = $mapping;
        $this->putJson(route('companies.update', $company->id), $company->toArray())
            ->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('t_companies', [
            'refs_custom_mapping->Ben' => $mapping['Ben'],
            'refs_custom_mapping->Joe' => $mapping['Joe'],
            'refs_custom_mapping->Peter' => $mapping['Peter'],
        ]);
    }

    /** @test */
    public function it_should__retrieve__company_by_id()
    {
        $company = factory(Company::class)->create();
        $this->getJson(route('companies.show', $company->id), $company->toArray())
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['id' => $company->id]);

        $this->assertDatabaseHas('t_companies', [
            'id' => $company->id
        ]);
    }

    /** @test */
    public function it_should_fail_if_not_authorized()
    {
        $this->loginNoAdmin();

        $this->getJson(route('companies.index'))->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
