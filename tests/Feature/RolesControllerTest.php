<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RolesControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_list_all_the_roles()
    {
        $this->loginAdmin();

        $this->getJson(route('roles.index'))
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'display_name',
                    ],
                ],
            ]);
    }
}
