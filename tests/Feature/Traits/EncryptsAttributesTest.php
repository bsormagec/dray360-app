<?php

namespace Tests\Feature\Traits;

use Tests\TestCase;
use App\Models\Address;
use App\Models\Company;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EncryptsAttributesTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_encrypt_the_attributes_when_saving()
    {
        $address = factory(Address::class)->create();
        $company = Company::create([
            't_address_id' => $address->id,
            'name' => 'test',
        ]);

        $originalValue = 'Test string';

        $company->update([
            'blackfly_token' => $originalValue,
            'ripcms_password' => $originalValue,
        ]);

        $this->assertDatabaseMissing('t_companies', [
            'blackfly_token' => $originalValue,
            'ripcms_password' => $originalValue,
        ]);
    }

    /** @test */
    public function it_should_decrypt_the_attributes()
    {
        $address = factory(Address::class)->create();
        $company = Company::create([
            't_address_id' => $address->id,
            'name' => 'test',
        ]);

        $originalValue = 'Test string';

        $company->update([
            'blackfly_token' => $originalValue,
            'ripcms_password' => $originalValue,
        ]);

        $this->assertEquals($originalValue, $company->blackfly_token);
        $this->assertEquals($originalValue, $company->ripcms_password);
    }
}
