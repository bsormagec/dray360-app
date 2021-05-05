<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\TMSProvider;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TmsProvidersControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_return_a_list_of_tms_providers_and_allow_filtering_sorting()
    {
        $this->loginAdmin();
        factory(TMSProvider::class, 10)->create();

        $this->getJson(route('tms-providers.index'))
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJsonCount(10, 'data');
    }
}
