<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use App\Models\Account;
use App\Models\OCRRule;
use Tests\Seeds\UserSeed;
use App\Models\OCRVariant;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use Tests\Seeds\OCRRulesAssignmentSeed;
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
    public function it_should_list_all_the_available_rules()
    {
        $rules = factory(OCRRule::class, 5)->create();
        $rules->first()->delete();

        $this->getJson(route('ocr.rules.index'))
            ->assertStatus(200)
            ->assertJsonCount($rules->count() - 1, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['name', 'code', 'description']
                ]
            ]);
    }

    /** @test */
    public function it_should_list_all_the_available_rules_filtered_by_account_and_variant()
    {
        $this->withoutExceptionHandling();
        $this->seed(OCRRulesAssignmentSeed::class);
        $accounts = Account::all(['id']);
        $ocrVariant = OCRVariant::first(['id']);

        $this->getJson(route('ocr.rules.index', [
                'account_id' => $accounts->first()->id,
                'variant_id' => $ocrVariant->id,
            ]))
            ->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['name', 'code', 'description']
                ]
            ]);

        $this->getJson(route('ocr.rules.index', [
                'account_id' => $accounts->last()->id,
                'variant_id' => $ocrVariant->id,
            ]))
            ->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['name', 'code', 'description']
                ]
            ]);
    }

    /** @test */
    public function it_should_create_a_rule()
    {
        $rule = factory(OCRRule::class)->make();

        $this->postJson(route('ocr.rules.store'), $rule->toArray())
            ->assertStatus(Response::HTTP_CREATED)
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
                ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->assertJsonValidationErrors($fieldToValidate);
        }
    }

    /** @test */
    public function it_should_update_an_existing_rule()
    {
        $rule = factory(OCRRule::class)->create()->toArray();
        $rule['code'] = 'Some new amazing code.';

        $this->putJson(route('ocr.rules.update', $rule['id']), $rule)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['id'])
            ->assertJsonFragment(['code' => $rule['code']]);
    }

    /** @test */
    public function it_should_fail_validation_on_update()
    {
        $rule = factory(OCRRule::class)->create();
        $toValidate = ['name','description', 'code'];

        foreach ($toValidate as $fieldToValidate) {
            $newRule = $rule->toArray();
            $newRule[$fieldToValidate] = null;

            $this->putJson(route('ocr.rules.update', $newRule['id']), $newRule)
                ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->assertJsonValidationErrors($fieldToValidate);
        }
    }
}
