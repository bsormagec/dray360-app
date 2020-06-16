<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SearchAddressControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_forwards_the_call_and_the_parameters_to_the_amazon_lamda_function_and_returns_the_response()
    {
        $this->withoutExceptionHandling();
        $this->loginAdmin();
        Http::fakeSequence()
            ->push(["event_info" => [], "address_list" => []]);
        Config::set('services.search-address.api_key', 'theapikey');
        Config::set('services.search-address.url', 'http://thesearchaddressurl.com');
        $data = [
            'is_terminal_address' => true,
            'is_tms_provider_address' => true,
            'is_canonical_address' => true,
            'location_nam' => 'test',
            'rawtext' => 'test',
            'postal_code' => 'test',
            'city' => 'test',
            'address' => 'test',
            'county' => 'test',
            'state' => 'test',
            'country' => 'test',
            'company_id' => 'test',
            'tms_provider_id' => 'test',
        ];

        $this->getJson(route('search-address.index', $data))
            ->assertJsonStructure(['event_info', 'address_list'])
            ->assertStatus(200);

        Http::assertSent(function ($request) use ($data) {
            return $request->hasHeader('X-API-KEY', 'theapikey') &&
                Str::contains($request->url(), 'http://thesearchaddressurl.com') &&
                count($data) == collect($data)
                    ->map(fn ($item, $key) => $request[$key] == $item)
                    ->sum();
        });
    }
}
