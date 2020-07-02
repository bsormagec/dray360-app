<?php

namespace Tests\Seeds;

use App\Models\Company;
use App\Models\OCRRule;
use App\Models\OCRVariant;
use Illuminate\Database\Seeder;
use App\Models\CompanyOCRVariantOCRRule;

class OCRRulesAssignmentSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = factory(Company::class, 2)->create();
        $ocrVariant = factory(OCRVariant::class)->create();
        $rulesCompany1 = factory(OCRRule::class, 2)->create();
        $rulesCompany2 = factory(OCRRule::class, 3)->create();

        foreach ($rulesCompany1 as $key => $rule) {
            $ocrRuleAssignment = new CompanyOCRVariantOCRRule();
            $ocrRuleAssignment->ocrRule()->associate($rule);
            $ocrRuleAssignment->ocrVariant()->associate($ocrVariant);
            $ocrRuleAssignment->company()->associate($companies->first());
            $ocrRuleAssignment->rule_sequence = $rulesCompany1->count() - $key;
            $ocrRuleAssignment->save();
        }

        foreach ($rulesCompany2 as $key => $rule) {
            $ocrRuleAssignment = new CompanyOCRVariantOCRRule();
            $ocrRuleAssignment->ocrRule()->associate($rule);
            $ocrRuleAssignment->ocrVariant()->associate($ocrVariant);
            $ocrRuleAssignment->company()->associate($companies->last());
            $ocrRuleAssignment->rule_sequence = $rulesCompany2->count() - $key;
            $ocrRuleAssignment->save();
        }
    }
}
