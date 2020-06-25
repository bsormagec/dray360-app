<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\OCRRule;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OCRRulesControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->loginAdmin();
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
    public function it_should_fail_if_not_authorized_to_edit_rules()
    {
        factory(OCRRule::class, 5)->create();
        $user = User::whereRoleIs('customer-user')->first();
        Sanctum::actingAs($user);

        $this->getJson(route('ocr.rules.index'))->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_should_create_a_rule()
    {
        $rule = factory(OCRRule::class)->make();

        $this->postJson(route('ocr.rules.store'), $rule->toArray())
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['id'])
            ->assertJsonFragment(['code' => $rule->code]);

        $this->assertDatabaseCount((new OCRRule())->getTable(), 1);
    }

    /** @test */
    public function it_should_fail_the_validation()
    {
        $toValidate = ['name', 'description', 'code'];

        foreach ($toValidate as $fieldToValidate) {
            $rule = factory(OCRRule::class)->make([$fieldToValidate => null]);

            $this->postJson(route('ocr.rules.store'), $rule->toArray())
                ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->assertJsonValidationErrors($fieldToValidate);
        }

        $this->assertDatabaseCount((new OCRRule())->getTable(), 0);
    }

    /** @test */
    public function it_should_fail_if_not_authorized_to_create_new_rules()
    {
        $user = User::whereRoleIs('customer-user')->first();
        Sanctum::actingAs($user);

        $rule = factory(OCRRule::class)->make();

        $this->postJson(route('ocr.rules.store'), $rule->toArray())
            ->assertStatus(Response::HTTP_FORBIDDEN);
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

        $table = (new OCRRule())->getTable();

        $this->assertDatabaseHas($table, ['code' => $rule['code']]);
    }

    /** @test */
    public function it_should_fail_validation_on_update()
    {
        $rule = factory(OCRRule::class)->create();
        $toValidate = ['name', 'description', 'code'];

        foreach ($toValidate as $fieldToValidate) {
            $newRule = $rule->toArray();
            $newRule[$fieldToValidate] = null;

            $this->putJson(route('ocr.rules.update', $newRule['id']), $newRule)
                ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->assertJsonValidationErrors($fieldToValidate);
        }
    }

    /** @test */
    public function it_should_fail_if_not_authorized_to_update_a_rule()
    {
        $user = User::whereRoleIs('customer-user')->first();
        Sanctum::actingAs($user);

        $rule = factory(OCRRule::class)->create()->toArray();
        $rule['code'] = 'Some new amazing code.';

        $this->putJson(route('ocr.rules.update', $rule['id']), $rule)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
