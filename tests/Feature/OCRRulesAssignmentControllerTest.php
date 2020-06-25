<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\OCRRule;
use App\Models\OCRVariant;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use Tests\Seeds\OCRRulesAssignmentSeed;
use App\Models\AccountOCRVariantOCRRule;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OCRRulesAssignmentControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->loginAdmin();
    }

    /** @test */
    public function it_should_update_the_assignment_of_rules_for_a_give_account_and_variant()
    {
        $this->seed(OCRRulesAssignmentSeed::class);
        $ocrVariant = OCRVariant::first(['id']);
        $account = Account::first(['id']);
        $ocrRules = OCRRule::all(['id']);

        $this->postJson(route('ocr.rules-assignment.store'), [
                'variant_id' => $ocrVariant->id,
                'account_id' => $account->id,
                'rules' => $ocrRules->pluck('id'),
            ])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonCount($ocrRules->count(), 'data');

        $this->assertCount($ocrRules->count(), AccountOCRVariantOCRRule::assignedTo(
            $account->id,
            $ocrVariant->id
        )->get());
        $this->assertNotNull(AccountOCRVariantOCRRule::assignedTo(
            $account->id,
            $ocrVariant->id
        )->first()->created_at);
    }

    /** @test */
    public function it_should_fail_validation()
    {
        $this->seed(OCRRulesAssignmentSeed::class);
        $ocrVariant = OCRVariant::first(['id']);
        $account = Account::first(['id']);
        $ocrRules = OCRRule::all(['id']);
        $toValidate = ['variant_id', 'account_id', 'rules'];

        foreach ($toValidate as $fieldToValidate) {
            $data = [
                'variant_id' => $ocrVariant->id,
                'account_id' => $account->id,
                'rules' => $ocrRules->pluck('id'),
            ];
            $data[$fieldToValidate] = null;

            $this->postJson(route('ocr.rules-assignment.store'), $data)
                ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->assertJsonValidationErrors($fieldToValidate);
        }
    }

    /** @test */
    public function it_should_not_allow_updating_the_assignment_if_not_authorized()
    {
        $user = User::whereRoleIs('customer-user')->first();
        Sanctum::actingAs($user);

        $this->seed(OCRRulesAssignmentSeed::class);
        $ocrVariant = OCRVariant::first(['id']);
        $account = Account::first(['id']);
        $ocrRules = OCRRule::all(['id']);

        $this->postJson(route('ocr.rules-assignment.store'), [
                'variant_id' => $ocrVariant->id,
                'account_id' => $account->id,
                'rules' => $ocrRules->pluck('id'),
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_should_list_all_the_available_rules_filtered_by_account_and_variant()
    {
        $this->withoutExceptionHandling();
        $this->seed(OCRRulesAssignmentSeed::class);
        $accounts = Account::all(['id']);
        $ocrVariant = OCRVariant::first(['id']);
        $rulesAccount1 = AccountOCRVariantOCRRule::query()
            ->assignedTo($accounts->first()->id, $ocrVariant->id)
            ->with('ocrRule')
            ->orderBy('rule_sequence')
            ->get()
            ->pluck('ocrRule');
        $rulesAccount2 = AccountOCRVariantOCRRule::query()
            ->assignedTo($accounts->last()->id, $ocrVariant->id)
            ->with('ocrRule')
            ->orderBy('rule_sequence')
            ->get()
            ->pluck('ocrRule');

        $this->getJson(route('ocr.rules-assignment.index', [
                'account_id' => $accounts->first()->id,
                'variant_id' => $ocrVariant->id,
            ]))
            ->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['name', 'code', 'description']
                ]
            ])
            ->assertJsonPath('data.0.id', $rulesAccount1->get(0)->id)
            ->assertJsonPath('data.1.id', $rulesAccount1->get(1)->id);

        $this->getJson(route('ocr.rules-assignment.index', [
                'account_id' => $accounts->last()->id,
                'variant_id' => $ocrVariant->id,
            ]))
            ->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['name', 'code', 'description']
                ]
            ])
            ->assertJsonPath('data.0.id', $rulesAccount2->get(0)->id)
            ->assertJsonPath('data.1.id', $rulesAccount2->get(1)->id)
            ->assertJsonPath('data.2.id', $rulesAccount2->get(2)->id);
    }

    /** @test */
    public function it_should_only_list_the_not_soft_deleted_associations()
    {
        $this->withoutExceptionHandling();
        $this->seed(OCRRulesAssignmentSeed::class);
        $accounts = Account::all(['id']);
        $ocrVariant = OCRVariant::first(['id']);
        $rulesAccount1 = AccountOCRVariantOCRRule::query()
            ->assignedTo($accounts->first()->id, $ocrVariant->id)
            ->with('ocrRule')
            ->orderBy('rule_sequence')
            ->get()
            ->tap(fn ($rules) => $rules->first()->delete())
            ->pluck('ocrRule');
        $rulesAccount2 = AccountOCRVariantOCRRule::query()
            ->assignedTo($accounts->last()->id, $ocrVariant->id)
            ->with('ocrRule')
            ->orderBy('rule_sequence')
            ->get()
            ->tap(fn ($rules) => $rules->first()->delete())
            ->pluck('ocrRule');

        $this->getJson(route('ocr.rules-assignment.index', [
                'account_id' => $accounts->first()->id,
                'variant_id' => $ocrVariant->id,
            ]))
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $rulesAccount1->get(1)->id);

        $this->getJson(route('ocr.rules-assignment.index', [
                'account_id' => $accounts->last()->id,
                'variant_id' => $ocrVariant->id,
            ]))
            ->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['name', 'code', 'description']
                ]
            ])
            ->assertJsonPath('data.0.id', $rulesAccount2->get(1)->id)
            ->assertJsonPath('data.1.id', $rulesAccount2->get(2)->id);
    }

    /** @test */
    public function it_should_not_list_the_assignments_if_not_authorized()
    {
        $user = User::whereRoleIs('customer-user')->first();
        Sanctum::actingAs($user);

        $this->seed(OCRRulesAssignmentSeed::class);
        $accounts = Account::all(['id']);
        $ocrVariant = OCRVariant::first(['id']);

        $this->getJson(route('ocr.rules-assignment.index', [
                'account_id' => $accounts->first()->id,
                'variant_id' => $ocrVariant->id,
            ]))
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->getJson(route('ocr.rules-assignment.index', [
                'account_id' => $accounts->last()->id,
                'variant_id' => $ocrVariant->id,
            ]))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
