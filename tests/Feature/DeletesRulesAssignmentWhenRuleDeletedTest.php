<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Company;
use App\Models\OCRVariant;
use Tests\Seeds\OCRRulesAssignmentSeed;
use App\Models\CompanyOCRVariantOCRRule;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DeletesRulesAssignmentWhenRuleDeletedTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_delete_all_the_assignments_when_the_rule_is_deleted()
    {
        $this->withoutExceptionHandling();
        $this->seed(OCRRulesAssignmentSeed::class);
        $companies = Company::all(['id']);
        $ocrVariant = OCRVariant::first(['id']);
        $companyVariantRule = CompanyOCRVariantOCRRule::query()
            ->assignedTo($companies->first()->id, $ocrVariant->id)
            ->with('ocrRule')
            ->first();
        $ruleCompany2 = CompanyOCRVariantOCRRule::query()
            ->assignedTo($companies->last()->id, $ocrVariant->id)
            ->orderBy('rule_sequence')
            ->first();

        $companyVariantRule->ocrRule->delete();

        $this->assertSoftDeleted('t_company_ocrvariant_ocrrules', [
            'id' => $companyVariantRule->id,
        ]);
        $this->assertDatabaseHas('t_company_ocrvariant_ocrrules', [
            'id' => $ruleCompany2->id,
            'deleted_at' => null,
        ]);
    }
}
