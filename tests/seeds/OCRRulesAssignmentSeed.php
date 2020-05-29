<?php

namespace Tests\Seeds;

use App\Models\Account;
use App\Models\OCRRule;
use App\Models\OCRVariant;
use Illuminate\Database\Seeder;
use App\Models\AccountOCRVariantOCRRule;

class OCRRulesAssignmentSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $accounts = factory(Account::class, 2)->create();
        $ocrVariant = factory(OCRVariant::class)->create();
        $rulesAccount1 = factory(OCRRule::class, 2)->create();
        $rulesAccount2 = factory(OCRRule::class, 3)->create();

        foreach ($rulesAccount1 as $key => $rule) {
            $ocrRuleAssignment = new AccountOCRVariantOCRRule();
            $ocrRuleAssignment->ocrRule()->associate($rule);
            $ocrRuleAssignment->ocrVariant()->associate($ocrVariant);
            $ocrRuleAssignment->account()->associate($accounts->first());
            $ocrRuleAssignment->rule_sequence = $rulesAccount1->count() - $key;
            $ocrRuleAssignment->save();
        }

        foreach ($rulesAccount2 as $key => $rule) {
            $ocrRuleAssignment = new AccountOCRVariantOCRRule();
            $ocrRuleAssignment->ocrRule()->associate($rule);
            $ocrRuleAssignment->ocrVariant()->associate($ocrVariant);
            $ocrRuleAssignment->account()->associate($accounts->last());
            $ocrRuleAssignment->rule_sequence = $rulesAccount2->count() - $key;
            $ocrRuleAssignment->save();
        }
    }
}
