<?php

// usage: php artisan db:seed --class=OCRRulesTableSeeder


use Illuminate\Database\Seeder;

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
    updated_fields["bol"] = input_fields["mbol"]
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
        // create an address
        $addressId = $this->seedAddress();

        // create account
        $accountId = $this->seedAccount('Cushing', $addressId);

        // create a variant
        $ocrvariantId = $this->seedVariant(5, 'Jetspeed-Import', 'Prenote from Cushing\'s Feight Buyer: Jetspeed');

        // create two rules
        $ocrrule1Id = $this->seedOCRRule('always_import', 'sample rule always_import', self::RULE1_CODE);
        $ocrrule2Id = $this->seedOCRRule('bol_to_mbol', 'sample rule bol_to_mbol', self::RULE2_CODE);
        $ocrrule3Id = $this->seedOCRRule('origin_always_null', 'Origin always null', self::RULE3_CODE);

        // create two accountvariantrule entries (use sequence)
        $this->seedAccountVariantRuleSequence($accountId, $ocrvariantId, $ocrrule1Id, 1); // rule sequence number 1
        $this->seedAccountVariantRuleSequence($accountId, $ocrvariantId, $ocrrule2Id, 2); // rule sequence number 2
        $this->seedAccountVariantRuleSequence($accountId, $ocrvariantId, $ocrrule3Id, 3); // rule sequence number 3
    }

    /**
     * Create an address (an empty address)
     *
     * @return int
     */
    public function seedAddress()
    {
        $address = App\Models\Address::create();
        return $address->id;
    }

    /**
     * Create an Account
     *
     * @return int
     */
    public function seedAccount($name, $addressId)
    {
        $account = App\Models\Account::create(
            [
                'name' => $name,
                't_address_id' => $addressId
            ]
        );

        return $account->id;
    }

    /**
     * Create a Variant named Jetspeed (id=5)
     *
     * @return int
     */
    public function seedVariant($abbyyVariantId, $abbyyVariantName, $description)
    {
        $variant = App\Models\OCRVariant::create(
            [
                'abbyy_variant_id' => $abbyyVariantId,
                'abbyy_variant_name' => $abbyyVariantName,
                'description' => $description
            ]
        );

        return $variant->id;
    }

    /**
     * Create an ocrrule
     *
     * @return int
     */
    public function seedOCRRule($name, $description, $code)
    {
        // $ocrrule1Id = DB::table('t_ocrrules')->insertGetId([
        //     'name' => $name,
        //     'description' => $description,
        //     'code' => $code
        // ]);

        $rule = App\Models\OCRRule::create(
            [
                'name' => $name,
                'description' => $description,
                'code' => $code
            ]
        );

        return $rule->id;
    }

    /**
     * Create account/variant/rule association
     *
     * @return int
     */
    public function seedAccountVariantRuleSequence($accountId, $ocrvariantId, $ocrruleId, $ruleSequence)
    {
        $account = App\Models\AccountOCRVariantOCRRule::create(
            [
                't_account_id' => $accountId,
                't_ocrvariant_id' => $ocrvariantId,
                't_ocrrule_id' => $ocrruleId,
                'rule_sequence' => $ruleSequence
            ]
        );

        return $account->id;
    }
}
