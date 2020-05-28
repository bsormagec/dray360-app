<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use App\Models\OCRRule;
use Tests\Seeds\UserSeed;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OCRRulesControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeed::class);
        $user = User::first();
        Sanctum::actingAs($user, ['*']);
    }

    /** @test */
    public function it_should_create_a_rule()
    {
        $rule = factory(OCRRule::class)->make();

        $this->postJson(route('ocr.rules.store'), $rule->toArray())
            ->assertStatus(201)
            ->assertJsonStructure(['id'])
            ->assertJsonFragment(['code' => $rule->code]);
    }

    /** @test */
    public function it_should_fail_the_validation()
    {
        $toValidate = ['name','description', 'code'];

        foreach ($toValidate as $fieldToValidate) {
            $rule = factory(OCRRule::class)->make([$fieldToValidate => null]);

            $this->postJson(route('ocr.rules.store'), $rule->toArray())
                ->assertStatus(422)
                ->assertJsonValidationErrors($fieldToValidate);
        }
    }
}
