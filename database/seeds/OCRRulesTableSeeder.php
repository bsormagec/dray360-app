<?php

// usage: php artisan db:seed --class=OCRRulesTableSeeder


use Illuminate\Database\Seeder;
use Carbon\Carbon;




class OCRRulesTableSeeder extends Seeder
{
    // class constants

    // two sample rules, from Aaron Bryden
    // rule 1
    CONST RULE1_CODE=<<<PYTHONCODE
possible_output_fields = {"shipment_type":{"always":True}}
def runrule(input_fields, updated_fields):
    #return {"shipment_type":"export"}
    updated_fields["shipment_type"] = "export"
PYTHONCODE;

    // rule2
    CONST RULE2_CODE=<<<PYTHONCODE
possible_output_fields = {"mbol":{"always":True}}
def runrule(input_fields, updated_fields):
    #return {"shipment_type":"export"}
    updated_fields["bol"] = input_fields["mbol"]
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
        $ocrvariantId = $this->seedVariant(5, 'Jetspeed', 'Prenote from Cushing\'s Feight Buyer: Jetspeed');

        // create two rules
        $ocrrule1Id = $this->seedOCRRule('always_export', 'sample rule always_export', self::RULE1_CODE);
        $ocrrule2Id = $this->seedOCRRule('bol_to_mbol', 'sample rule bol_to_mbol', self::RULE2_CODE);

        // create two accountvariantrule entries (use sequence)
        $this->seedAccountVariantRuleSequence($accountId, $ocrvariantId, $ocrrule1Id, 1); // rule sequence number 1
        $this->seedAccountVariantRuleSequence($accountId, $ocrvariantId, $ocrrule2Id, 2); // rule sequence number 2
    }




    /**
     * Create an address (an empty address)
     *
     * @return int
     */
    public function seedAddress()
    {
        $addressId = DB::table('t_addresses')->insertGetId([
        ]);

        return $addressId;
    }



    /**
     * Create an Account
     *
     * @return int
     */
    public function seedAccount($name, $addressId)
    {
        $accountId = DB::table('t_accounts')->insertGetId([
            'name' => $name,
            't_address_id' => $addressId
        ]);

        return $accountId;
    }



    /**
     * Create a Variant named Jetspeed (id=5)
     *
     * @return int
     */
    public function seedVariant($abbyyVariantId, $abbyyVariantName, $description)
    {
        $variantId = DB::table('t_ocrvariants')->insertGetId([
            'abbyy_variant_id' => $abbyyVariantId,
            'abbyy_variant_name' => $abbyyVariantName,
            'description' => $description
        ]);

        return $variantId;
    }



    /**
     * Create an ocrrule
     *
     * @return int
     */
    public function seedOCRRule($name, $description, $code)
    {
        $ocrrule1Id = DB::table('t_ocrrules')->insertGetId([
            'name' => $name,
            'description' => $description,
            'code' => $code
        ]);

        return $ocrrule1Id;
    }



    /**
     * Create account/variant/rule association
     *
     * @return int
     */
    public function seedAccountVariantRuleSequence($accountId, $ocrvariantId, $ocrruleId, $ruleSequence)
    {
        $avrId = DB::table('t_account_ocrvariant_ocrrules')->insertGetId([
            't_account_id' => $accountId,
            't_ocrvariant_id' => $ocrvariantId,
            't_ocrrule_id' => $ocrruleId,
            'rule_sequence' => $ruleSequence
        ]);

        return $avrId;
    }

}
