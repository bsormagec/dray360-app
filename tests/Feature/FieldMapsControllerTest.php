<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\FieldMap;
use App\Models\OCRVariant;
use App\Models\TMSProvider;
use Illuminate\Http\Response;
use Tests\Seeds\CompaniesSeeder;
use App\Models\CompanyOcrVariant;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FieldMapsControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected Company $company;
    protected User $user;
    protected FieldMap $systemDefaultMap;
    protected TMSProvider $tmsProvider;
    protected OCRVariant $ocrVariant;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginAdmin();

        $this->seed(CompaniesSeeder::class);

        $this->company = CompaniesSeeder::getTestTcompanies();
        $this->user = auth()->user();
        $this->user->setCompany($this->company, true);
        $this->systemDefaultMap = factory(FieldMap::class)->create([
            'system_default' => true,
            'fieldmap_config' => ['main' => ['something' => false]],
        ]);

        $fieldMap = factory(FieldMap::class)->create([
            'fieldmap_config' => ['main' => ['another' => 1], 'secondary' => ['something2' => true]],
        ]);
        $this->tmsProvider = $this->company->defaultTmsProvider;
        $this->tmsProvider->update(['t_fieldmap_id' => $fieldMap->id]);

        $fieldMap = factory(FieldMap::class)->create([
            'fieldmap_config' => ['main' => ['something' => true], 'secondary' => ['something3' => false]],
        ]);
        $this->company->update(['t_fieldmap_id' => $fieldMap->id]);

        $fieldMap = factory(FieldMap::class)->create([
            'fieldmap_config' => ['main' => ['another2' => true], 'secondary' => ['something4' => false]],
        ]);
        $this->ocrVariant = factory(OCRVariant::class)->create();
        $this->ocrVariant->update(['t_fieldmap_id' => $fieldMap->id]);

        $fieldMap = factory(FieldMap::class)->create([
            'fieldmap_config' => new \stdClass(),
        ]);
        CompanyOcrVariant::create([
            't_company_id' => $this->company->id,
            't_ocrvariant_id' => $this->ocrVariant->id,
            't_fieldmap_id' => $fieldMap->id
        ]);
    }

    /** @test */
    public function it_should_return_the_field_map_for_the_system_default()
    {
        $this->getJson(route('field-maps.index'))
            ->assertJsonFragment(['something' => false])
            ->assertJsonStructure([
                'data' => [
                    'current' => [
                        'main' => [
                            'something',
                        ],
                    ],
                    'previous',
                ],
            ]);
    }

    /** @test */
    public function it_should_return_the_field_map_for_the_system_default_plus_tms_provider()
    {
        $this->getJson(route('field-maps.index', [
                'tms_provider_id' => $this->tmsProvider->id
            ]))
            ->assertJsonFragment(['something2' => true])
            ->assertJsonFragment(['another' => 1])
            ->assertJsonStructure([
                'data' => [
                    'current' => [
                        'main' => [
                            'something',
                            'another',
                        ],
                        'secondary' => [
                            'something2',
                        ],
                    ],
                    'previous' => [
                        'main' => [
                            'something',
                        ],
                    ]
                ],
            ]);
    }

    /** @test */
    public function it_should_return_the_field_map_for_the_system_default_plus_company_id_with_its_tms_provider()
    {
        $this->getJson(route('field-maps.index', [
                'company_id' => $this->company->id,
            ]))
            ->assertJsonFragment(['something' => true])
            ->assertJsonFragment(['something3' => false])
            ->assertJsonStructure([
                'data' => [
                    'current' => [
                        'main' => [
                            'something',
                            'another'
                        ],
                        'secondary' => [
                            'something2',
                            'something3',
                        ],
                    ],
                    'previous' => [
                        'main' => [
                            'something',
                            'another',
                        ],
                        'secondary' => [
                            'something2',
                        ],
                    ],
                ],
            ]);
    }

    /** @test */
    public function it_should_return_the_field_map_for_the_system_default_plus_ocrvariant()
    {
        $this->getJson(route('field-maps.index', [
                'variant_id' => $this->ocrVariant->id,
            ]))
            ->assertJsonFragment(['another2' => true])
            ->assertJsonFragment(['something4' => false])
            ->assertJsonStructure([
                'data' => [
                    'current' => [
                        'main' => [
                            'something',
                            'another2',
                        ],
                        'secondary' => [
                            'something4',
                        ],
                    ],
                    'previous',
                ],
            ]);
    }

    /** @test */
    public function it_should_return_the_field_map_for_the_system_default_plus_ocrvariant_company_with_its_tms_provider()
    {
        $this->getJson(route('field-maps.index', [
                'variant_id' => $this->ocrVariant->id,
                'company_id' => $this->company->id,
            ]))
            ->assertJsonStructure([
                'data' => [
                    'current' => [
                        'main' => [
                            'something',
                            'another',
                            'another2',
                        ],
                        'secondary' => [
                            'something2',
                            'something3',
                            'something4',
                        ],
                    ],
                    'previous',
                ],
            ]);
    }

    /** @test */
    public function it_updates_the_default_when_nothing_provided()
    {
        $initialFieldMap = FieldMap::getSystemDefault();
        $this->postJson(route('field-maps.store'), [
                'fieldmap_config' => ['main' => ['something' => false, 'newdefault' => 1]],
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'fieldmap' => [
                    'created_at',
                    'fieldmap_config' => [
                        'main' => ['something', 'newdefault'],
                    ],
                ],
                'changes',
            ]);

        $finalFieldMap = FieldMap::getSystemDefault();
        $this->assertDatabaseHas('t_fieldmaps', [
            'id' => $finalFieldMap->id,
            'replacedby_id' => null,
            'system_default' => true,
        ]);
        $this->assertEquals(json_encode($finalFieldMap->fieldmap_config), json_encode(['main' => ['something' => false, 'newdefault' => 1]]));
        $this->assertEquals($initialFieldMap->id, $finalFieldMap->id);
    }

    /** @test */
    public function it_only_saves_the_difference_from_the_previous_level_when_only_tms_provider()
    {
        $this->tmsProvider->refresh();
        $initialFieldMapId = $this->tmsProvider->t_fieldmap_id;
        $this->postJson(route('field-maps.store'), [
                'tms_provider_id' => $this->tmsProvider->id,
                'fieldmap_config' => ['main' => ['another' => 2, 'something' => false]],
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'fieldmap' => [
                    'created_at',
                    'fieldmap_config' => [
                        'main' => ['another'],
                    ],
                ],
                'changes',
            ]);

        $this->tmsProvider->refresh();
        $finalFieldMap = $this->tmsProvider->fieldMap;
        $this->assertDatabaseHas('t_fieldmaps', [
            'id' => $finalFieldMap->id,
            'replacedby_id' => null,
        ]);
        $this->assertEquals(json_encode($finalFieldMap->fieldmap_config), json_encode(['main' => ['another' => 2]]));
        $this->assertEquals($initialFieldMapId, $finalFieldMap->id);
    }

    /** @test */
    public function it_only_saves_the_difference_from_the_previous_level_when_only_company_id()
    {
        $this->company->refresh();
        $initialFieldMapId = $this->company->t_fieldmap_id;
        $this->postJson(route('field-maps.store'), [
                'company_id' => $this->company->id,
                'fieldmap_config' => FieldMap::getFrom(['tms_provider_id' => $this->company->default_tms_provider_id]),
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['fieldmap_config' => []])
            ->assertJsonStructure([
                'fieldmap' => [
                    'created_at',
                    'fieldmap_config',
                ],
                'changes',
            ]);

        $this->company->refresh();
        $finalFieldMap = $this->company->fieldMap;
        $this->assertDatabaseHas('t_fieldmaps', [
            'id' => $finalFieldMap->id,
            'replacedby_id' => null,
        ]);
        $this->assertEquals(json_encode($finalFieldMap->fieldmap_config), '[]');
        $this->assertEquals($initialFieldMapId, $finalFieldMap->id);
    }

    /** @test */
    public function it_doesnt_delete_the_company_configuration_when_saving_the_field_maps()
    {
        $this->company->update(['configuration' => ['test']]);
        $this->company->refresh();
        $this->postJson(route('field-maps.store'), [
                'company_id' => $this->company->id,
                'fieldmap_config' => FieldMap::getFrom(['tms_provider_id' => $this->company->default_tms_provider_id]),
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['fieldmap_config' => []])
            ->assertJsonStructure([
                'fieldmap' => [
                    'created_at',
                    'fieldmap_config',
                ],
                'changes',
            ]);

        $this->company->refresh();
        $this->assertEquals(json_encode(['test']), json_encode($this->company->configuration));
    }

    /** @test */
    public function it_only_saves_the_difference_from_the_previous_level_when_only_variant_id()
    {
        $this->ocrVariant->refresh();
        $initialFieldMapId = $this->ocrVariant->t_fieldmap_id;
        $this->postJson(route('field-maps.store'), [
                'variant_id' => $this->ocrVariant->id,
                'fieldmap_config' => FieldMap::getFrom([]),
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['fieldmap_config' => []])
            ->assertJsonStructure([
                'fieldmap' => [
                    'created_at',
                    'fieldmap_config',
                ],
                'changes',
            ]);

        $this->ocrVariant->refresh();
        $finalFieldMap = $this->ocrVariant->fieldMap;
        $this->assertDatabaseHas('t_fieldmaps', [
            'id' => $finalFieldMap->id,
            'replacedby_id' => null,
        ]);
        $this->assertEquals(json_encode($finalFieldMap->fieldmap_config), '[]');
        $this->assertEquals($initialFieldMapId, $finalFieldMap->id);
    }

    /** @test */
    public function it_only_saves_the_difference_from_the_previous_level_when_variant_id_and_company_id()
    {
        $companyOcrVariant = CompanyOcrVariant::first();
        $initialFieldMapId = $companyOcrVariant->t_fieldmap_id;
        $this->postJson(route('field-maps.store'), [
                'company_id' => $this->company->id,
                'variant_id' => $this->ocrVariant->id,
                'fieldmap_config' => FieldMap::getFrom([
                    'company_id' => $this->company->id,
                    'variant_id' => $this->ocrVariant->id,
                ], false),
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['fieldmap_config' => []])
            ->assertJsonStructure([
                'fieldmap' => [
                    'created_at',
                    'fieldmap_config',
                ],
                'changes',
            ]);

        $companyOcrVariant->refresh();
        $finalFieldMap = $companyOcrVariant->fieldMap;
        $this->assertDatabaseHas('t_fieldmaps', [
            'id' => $finalFieldMap->id,
            'replacedby_id' => null,
        ]);
        $this->assertEquals(json_encode($finalFieldMap->fieldmap_config), '[]');
        $this->assertEquals($initialFieldMapId, $finalFieldMap->id);
    }
}
