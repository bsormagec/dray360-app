<?php

namespace Tests\Feature\Jobs;

use Mockery;
use Exception;
use Tests\TestCase;
use App\Models\Company;
use App\Models\DictionaryItem;
use App\Jobs\Imports\ImportDictionaryItems;
use Tests\MockClasses\MockDictionaryItemImporter;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ImportDictionaryItemsTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_run_the_default_handler()
    {
        $company = factory(Company::class)->create([
            'blackfly_token' => 'testToken',
        ]);
        /** @var \Mockery\Mock */
        $mock = Mockery::mock(ImportDictionaryItems::class)->makePartial();
        $mock->companyId = $company->id;
        $mock->syncSettings = [
            'item_type' => DictionaryItem::TEMPLATE_TYPE,
            'delete_missing' => false,
        ];
        $mock->itemType = DictionaryItem::TEMPLATE_TYPE;

        $mock->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('defaultTemplatesImporter')
            ->andReturns(MockDictionaryItemImporter::class)
            ->once();

        $mock->handle();

        $this->assertDatabaseHas('t_dictionary_items', [
            't_company_id' => $company->id,
            'item_type' => DictionaryItem::TEMPLATE_TYPE,
            'item_key' => MockDictionaryItemImporter::KEY,
            'item_display_name' => MockDictionaryItemImporter::DISPLAY_NAME,
        ]);
    }

    /** @test */
    public function it_should_run_the_alternative_handler()
    {
        $company = factory(Company::class)->create([
            'blackfly_token' => 'testToken',
        ]);

        $job = new ImportDictionaryItems($company, [
            'item_type' => DictionaryItem::TEMPLATE_TYPE,
            'delete_missing' => false,
            'alternate_handler' => MockDictionaryItemImporter::class,
        ]);

        $job->handle();

        $this->assertDatabaseHas('t_dictionary_items', [
            't_company_id' => $company->id,
            'item_type' => DictionaryItem::TEMPLATE_TYPE,
            'item_key' => MockDictionaryItemImporter::KEY,
            'item_display_name' => MockDictionaryItemImporter::DISPLAY_NAME,
        ]);
    }

    /** @test */
    public function it_should_fail_if_alternate_handler_doesnt_exist()
    {
        $company = factory(Company::class)->create([
            'blackfly_token' => 'testToken',
        ]);
        $job = new ImportDictionaryItems($company, [
            'item_type' => DictionaryItem::TEMPLATE_TYPE,
            'delete_missing' => false,
            'alternate_handler' => 'SomeClasThatDoesntExist',
        ]);

        $this->expectException(Exception::class);

        $job->handle();
    }
}
