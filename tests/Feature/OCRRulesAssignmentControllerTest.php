<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Account;
use App\Models\OCRRule;
use App\Models\OCRVariant;
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
        $toValidate = ['variant_id','account_id', 'rules'];

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
}
