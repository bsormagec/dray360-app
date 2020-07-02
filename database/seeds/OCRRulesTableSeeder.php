<?php

// usage: php artisan db:seed --class=OCRRulesTableSeeder

use App\Models\Company;
use App\Models\OCRRule;
use App\Models\OCRVariant;
use Illuminate\Database\Seeder;
use App\Models\CompanyOCRVariantOCRRule;

class OCRRulesTableSeeder extends Seeder
{
    // class constants

    // two sample rules, from Aaron Bryden
    // rule 1
    const RULE1_CODE = <<<PYTHONCODE
possible_output_fields = {"shipment_designation":{"always":True}}
def runrule(input_fields, updated_fields):
    #return {"shipment_type":"import"}
    updated_fields["shipment_designation"] = "import"
PYTHONCODE;

    // rule 2
    const RULE2_CODE = <<<PYTHONCODE
possible_output_fields = {"mbol":{"always":True}}
def runrule(input_fields, updated_fields):
    #return {"shipment_type":"export"}
    updated_fields["bill_of_lading"] = input_fields["mbol"]
PYTHONCODE;

    // rule 3
    const RULE3_CODE = <<<PYTHONCODE
possible_output_fields = {"port_ramp_of_origin_address_raw_text":{"always":True}}
def runrule(input_fields, updated_fields):
    updated_fields["port_ramp_of_origin_address_raw_text"] = None
PYTHONCODE;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companyId = factory(Company::class)->create(['name' => 'Cushing'])->id;

        // create a variant
        $ocrvariantId = $this->seedVariant(5, 'Jetspeed-Import', 'Prenote from Cushing\'s Feight Buyer: Jetspeed');

        // create two rules
        $ocrrule1Id = $this->seedOCRRule('always_import', 'sample rule always_import', self::RULE1_CODE);
        $ocrrule2Id = $this->seedOCRRule('bol_to_mbol', 'sample rule bol_to_mbol', self::RULE2_CODE);
        $ocrrule3Id = $this->seedOCRRule('origin_always_null', 'Origin always null', self::RULE3_CODE);

        $this->seedCompanyVariantRuleSequence($companyId, $ocrvariantId, $ocrrule1Id, 1); // rule sequence number 1
        $this->seedCompanyVariantRuleSequence($companyId, $ocrvariantId, $ocrrule2Id, 2); // rule sequence number 2
        $this->seedCompanyVariantRuleSequence($companyId, $ocrvariantId, $ocrrule3Id, 3); // rule sequence number 3
    }

    /**
     * Create a Variant named Jetspeed (id=5)
     *
     * @return int
     */
    public function seedVariant($abbyyVariantId, $abbyyVariantName, $description): int
    {
        return OCRVariant::create([
            'abbyy_variant_id' => $abbyyVariantId,
            'abbyy_variant_name' => $abbyyVariantName,
            'description' => $description
        ])->id;
    }

    /**
     * Create an ocrrule
     */
    public function seedOCRRule($name, $description, $code): int
    {
        return OCRRule::create([
            'name' => $name,
            'description' => $description,
            'code' => $code
        ])->id;
    }

    /**
     * Create company/variant/rule association
     */
    public function seedCompanyVariantRuleSequence($companyId, $ocrvariantId, $ocrruleId, $ruleSequence): int
    {
        return CompanyOCRVariantOCRRule::create([
            't_company_id' => $companyId,
            't_ocrvariant_id' => $ocrvariantId,
            't_ocrrule_id' => $ocrruleId,
            'rule_sequence' => $ruleSequence
        ])->id;
    }
}
