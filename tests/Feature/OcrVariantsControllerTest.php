<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\OCRVariant;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OcrVariantsControllerTest extends TestCase
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
        factory(OCRVariant::class, 10)->create();

        $this->getJson(route('ocr.variants.index'))
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'abbyy_variant_name',
                        'abbyy_variant_id',
                        'description',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links',
                'meta',
            ])
            ->assertJsonCount(10, 'data');
    }

    /** @test */
    public function it_should_fail_if_not_authorized()
    {
        $this->loginNoAdmin();

        $this->getJson(route('ocr.variants.index'))->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_should_allow_to_create_a_new_variant()
    {
        $ocrVariant = factory(OCRVariant::class)->make();

        $this->postJson(route('ocr.variants.store'), $ocrVariant->toArray())
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['id'])
            ->assertJsonFragment(['abbyy_variant_name' => $ocrVariant->abbyy_variant_name]);

        $this->assertDatabaseCount('t_ocrvariants', 1);
    }

    /** @test */
    public function it_should_fail_if_not_authorized_to_create_variants()
    {
        $this->loginNoAdmin();
        $ocrVariant = factory(OCRVariant::class)->make();

        $this->postJson(route('ocr.variants.store'), $ocrVariant->toArray())
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_should_allow_to_update_a_variant()
    {
        $ocrVariant = factory(OCRVariant::class)->create();
        $ocrVariant->abbyy_variant_name = 'test';

        $this->putJson(route('ocr.variants.update', $ocrVariant->id), $ocrVariant->toArray())
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['id'])
            ->assertJsonFragment(['abbyy_variant_name' => $ocrVariant->abbyy_variant_name]);

        $this->assertDatabaseHas('t_ocrvariants', [
            'abbyy_variant_name' => $ocrVariant->abbyy_variant_name
        ]);
    }

    /** @test */
    public function it_should_fail_if_not_authorized_to_update_variants()
    {
        $this->loginNoAdmin();
        $ocrVariant = factory(OCRVariant::class)->create();
        $ocrVariant->abbyy_variant_name = 'test';

        $this->putJson(route('ocr.variants.update', $ocrVariant->id), $ocrVariant->toArray())
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_should_allow_to_delete_a_variant()
    {
        $ocrVariant = factory(OCRVariant::class)->create();

        $this->deleteJson(route('ocr.variants.destroy', $ocrVariant->id))
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertSoftDeleted('t_ocrvariants', ['id' => $ocrVariant->id]);
    }

    /** @test */
    public function it_should_fail_if_not_authorized_to_delete_variants()
    {
        $this->loginNoAdmin();
        $ocrVariant = factory(OCRVariant::class)->create();

        $this->deleteJson(route('ocr.variants.destroy', $ocrVariant->id))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
