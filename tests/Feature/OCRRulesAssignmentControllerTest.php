<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\OCRRule;
use App\Models\OCRVariant;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use Tests\Seeds\OCRRulesAssignmentSeed;
use App\Models\CompanyOCRVariantOCRRule;
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
    public function it_should_update_the_assignment_of_rules_for_a_give_company_and_variant()
    {
        $this->seed(OCRRulesAssignmentSeed::class);
        $ocrVariant = OCRVariant::first(['id']);
        $company = Company::first(['id']);
        $ocrRules = OCRRule::all(['id']);

        $this->postJson(route('ocr.rules-assignment.store'), [
                'variant_id' => $ocrVariant->id,
                'company_id' => $company->id,
                'rules' => collect([])->push($ocrRules->first())->merge($ocrRules)->pluck('id'),
            ])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonCount($ocrRules->count(), 'data');

        $this->assertCount($ocrRules->count(), CompanyOCRVariantOCRRule::assignedTo(
            $company->id,
            $ocrVariant->id
        )->get());
        $this->assertNotNull(CompanyOCRVariantOCRRule::assignedTo(
            $company->id,
            $ocrVariant->id
        )->first()->created_at);
    }

    /** @test */
    public function it_should_update_the_rules_associated_with_only_variant()
    {
        $this->seed(OCRRulesAssignmentSeed::class);
        $ocrVariant = OCRVariant::first(['id']);
        $ocrRules = OCRRule::all(['id']);

        $ocrRuleAssignment = new CompanyOCRVariantOCRRule();
        $ocrRuleAssignment->ocrRule()->associate($ocrRules->first());
        $ocrRuleAssignment->ocrVariant()->associate($ocrVariant);
        $ocrRuleAssignment->rule_sequence = 0;
        $ocrRuleAssignment->save();

        $this->postJson(route('ocr.rules-assignment.store'), [
                'variant_id' => $ocrVariant->id,
                'rules' => $ocrRules->pluck('id'),
            ])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonCount($ocrRules->count(), 'data');

        $this->assertCount($ocrRules->count(), CompanyOCRVariantOCRRule::assignedTo(
            null,
            $ocrVariant->id
        )->get());
        $this->assertNotNull(CompanyOCRVariantOCRRule::assignedTo(
            null,
            $ocrVariant->id
        )->first()->created_at);
    }

    /** @test */
    public function it_should_fail_validation()
    {
        $this->seed(OCRRulesAssignmentSeed::class);
        $ocrVariant = OCRVariant::first(['id']);
        $company = Company::first(['id']);
        $ocrRules = OCRRule::all(['id']);
        $toValidate = ['variant_id', 'rules'];

        foreach ($toValidate as $fieldToValidate) {
            $data = [
                'variant_id' => $ocrVariant->id,
                'company_id' => $company->id,
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
        $company = Company::first(['id']);
        $ocrRules = OCRRule::all(['id']);

        $this->postJson(route('ocr.rules-assignment.store'), [
                'variant_id' => $ocrVariant->id,
                'company_id' => $company->id,
                'rules' => $ocrRules->pluck('id'),
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_should_list_all_the_available_rules_filtered_by_company_and_variant()
    {
        $this->withoutExceptionHandling();
        $this->seed(OCRRulesAssignmentSeed::class);
        $companies = Company::all(['id']);
        $ocrVariant = OCRVariant::first(['id']);
        $rulesCompany1 = CompanyOCRVariantOCRRule::query()
            ->assignedTo($companies->first()->id, $ocrVariant->id)
            ->with('ocrRule')
            ->orderBy('rule_sequence')
            ->get()
            ->pluck('ocrRule');
        $rulesCompany2 = CompanyOCRVariantOCRRule::query()
            ->assignedTo($companies->last()->id, $ocrVariant->id)
            ->with('ocrRule')
            ->orderBy('rule_sequence')
            ->get()
            ->pluck('ocrRule');
        $ocrRule = OCRRule::first(['id']);
        $ocrRuleAssignment = new CompanyOCRVariantOCRRule();
        $ocrRuleAssignment->ocrRule()->associate($ocrRule);
        $ocrRuleAssignment->ocrVariant()->associate($ocrVariant);
        $ocrRuleAssignment->rule_sequence = 0;
        $ocrRuleAssignment->save();

        $this->getJson(route('ocr.rules-assignment.index', [
                'variant_id' => $ocrVariant->id,
            ]))
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['name', 'code', 'description']
                ]
            ])
            ->assertJsonPath('data.0.id', $ocrRule->id);

        $this->getJson(route('ocr.rules-assignment.index', [
                'company_id' => $companies->first()->id,
                'variant_id' => $ocrVariant->id,
            ]))
            ->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['name', 'code', 'description']
                ]
            ])
            ->assertJsonPath('data.0.id', $rulesCompany1->get(0)->id)
            ->assertJsonPath('data.1.id', $rulesCompany1->get(1)->id);

        $this->getJson(route('ocr.rules-assignment.index', [
                'company_id' => $companies->last()->id,
                'variant_id' => $ocrVariant->id,
            ]))
            ->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['name', 'code', 'description']
                ]
            ])
            ->assertJsonPath('data.0.id', $rulesCompany2->get(0)->id)
            ->assertJsonPath('data.1.id', $rulesCompany2->get(1)->id)
            ->assertJsonPath('data.2.id', $rulesCompany2->get(2)->id);
    }

    /** @test */
    public function it_should_only_list_the_not_soft_deleted_associations()
    {
        $this->withoutExceptionHandling();
        $this->seed(OCRRulesAssignmentSeed::class);
        $companies = Company::all(['id']);
        $ocrVariant = OCRVariant::first(['id']);
        $rulesCompany1 = CompanyOCRVariantOCRRule::query()
            ->assignedTo($companies->first()->id, $ocrVariant->id)
            ->with('ocrRule')
            ->orderBy('rule_sequence')
            ->get()
            ->tap(fn ($rules) => $rules->first()->delete())
            ->pluck('ocrRule');
        $rulesCompany2 = CompanyOCRVariantOCRRule::query()
            ->assignedTo($companies->last()->id, $ocrVariant->id)
            ->with('ocrRule')
            ->orderBy('rule_sequence')
            ->get()
            ->tap(fn ($rules) => $rules->first()->delete())
            ->pluck('ocrRule');

        $this->getJson(route('ocr.rules-assignment.index', [
                'company_id' => $companies->first()->id,
                'variant_id' => $ocrVariant->id,
            ]))
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $rulesCompany1->get(1)->id);

        $this->getJson(route('ocr.rules-assignment.index', [
                'company_id' => $companies->last()->id,
                'variant_id' => $ocrVariant->id,
            ]))
            ->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['name', 'code', 'description']
                ]
            ])
            ->assertJsonPath('data.0.id', $rulesCompany2->get(1)->id)
            ->assertJsonPath('data.1.id', $rulesCompany2->get(2)->id);
    }

    /** @test */
    public function it_should_only_list_the_associations_with_not_deleted_rules()
    {
        $this->seed(OCRRulesAssignmentSeed::class);
        $companies = Company::all(['id']);
        $ocrVariant = OCRVariant::first(['id']);
        $rulesCompany1 = CompanyOCRVariantOCRRule::query()
            ->assignedTo($companies->first()->id, $ocrVariant->id)
            ->with('ocrRule')
            ->orderBy('rule_sequence')
            ->get()
            ->tap(fn ($rules) => $rules->first()->ocrRule->delete())
            ->pluck('ocrRule');

        $this->getJson(route('ocr.rules-assignment.index', [
                'company_id' => $companies->first()->id,
                'variant_id' => $ocrVariant->id,
            ]))
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $rulesCompany1->get(1)->id);
    }

    /** @test */
    public function it_should_not_list_the_assignments_if_not_authorized()
    {
        $user = User::whereRoleIs('customer-user')->first();
        Sanctum::actingAs($user);

        $this->seed(OCRRulesAssignmentSeed::class);
        $companies = Company::all(['id']);
        $ocrVariant = OCRVariant::first(['id']);

        $this->getJson(route('ocr.rules-assignment.index', [
                'company_id' => $companies->first()->id,
                'variant_id' => $ocrVariant->id,
            ]))
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->getJson(route('ocr.rules-assignment.index', [
                'company_id' => $companies->last()->id,
                'variant_id' => $ocrVariant->id,
            ]))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
