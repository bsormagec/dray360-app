<?php

namespace Database\Seeders;

/**
 * Usage: php artisan db:seed --class=DictionaryItemsImportSeeder
 */

use App\Models\Company;
use App\Models\DictionaryItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

/**
 * General Dictionaty Items import
 */
class DictionaryItemsImportSeeder extends Seeder
{
    const ALL_COMPANIES = [

        // ITG Onboarding
        'ITGOnboarding' => [
            [
                'file' => 'seeders/CargoWise One Export - 20210204142016 - 164.xlsx',
                'sheet' => 1,
                'columns' => [
                    'item_key' => 'Code',
                    'item_display_name' => 'Description',
                    'item_type' => DictionaryItem::CARRIER_TYPE,
                    'item_value' => [
                        'code' => 'Code',
                        'description' => 'Description',
                    ],
                ],
            ],
            [
                'file' => 'seeders/ITG David Duke - Vessel Listing 1.22.21.xlsx',
                'sheet' => 1,
                'columns' => [
                    'item_key' => 'Vessel',
                    'item_display_name' => 'Vessel',
                    'item_type' => DictionaryItem::VESSEL_TYPE,
                    'item_value' => [],
                ]
            ],
            [
                'file' => 'seeders/CargoWise One Export - 20210107113712 - 414.csv',
                'columns' => [
                    'item_key' => 'Code',
                    'item_display_name' => 'Description',
                    'item_type' => DictionaryItem::ITGCONTAINER_TYPE,
                    'item_value' => [
                        'code' => 'Code',
                        'description' => 'Description',
                        'mode' => 'Mode',
                        'container_type' => 'Container Type',
                        'times_used' => 'Times Used',
                    ],
                ]
            ],
        ],

        // ITG
        'ITG' => [
            [
                'file' => 'seeders/CargoWise One Export - 20210204142016 - 164.xlsx',
                'sheet' => 1,
                'columns' => [
                    'item_key' => 'Code',
                    'item_display_name' => 'Description',
                    'item_type' => DictionaryItem::CARRIER_TYPE,
                    'item_value' => [
                        'code' => 'Code',
                        'description' => 'Description',
                    ],
                ],
            ],
            [
                'file' => 'seeders/ITG David Duke - Vessel Listing 1.22.21.xlsx',
                'sheet' => 1,
                'columns' => [
                    'item_key' => 'Vessel',
                    'item_display_name' => 'Vessel',
                    'item_type' => DictionaryItem::VESSEL_TYPE,
                    'item_value' => [],
                ]
            ],
            [
                'file' => 'seeders/CargoWise One Export - 20210107113712 - 414.csv',
                'columns' => [
                    'item_key' => 'Code',
                    'item_display_name' => 'Description',
                    'item_type' => DictionaryItem::ITGCONTAINER_TYPE,
                    'item_value' => [
                        'code' => 'Code',
                        'description' => 'Description',
                        'mode' => 'Mode',
                        'container_type' => 'Container Type',
                        'times_used' => 'Times Used',
                    ],
                ]
            ],
        ],

        // Graham Trucking
        'GrahamTrucking' => [
            [
                'file' => 'seeders/graham_trucking_templates.20210322.xlsx',
                'columns' => [
                    'item_key' => 'TMP Number',
                    'item_display_name' => 'Template Name',
                    'item_type' => DictionaryItem::TEMPLATE_TYPE,
                    'item_value' => []
                ]
            ],
        ],

        // Mercer
        'Mercer' => [
            [
                'file' => 'seeders/MercerTemplates_20210610.xlsx',
                'columns' => [
                    'item_key' => 'TMP #',
                    'item_display_name' => 'Template Name',
                    'item_type' => DictionaryItem::TEMPLATE_TYPE,
                    'item_value' => []
                ]
            ],
        ],
    ];

    /**
     * Put companies to actually import into this list. Normally a
     * company won't need to be seeded twice.
     */
    const TO_BE_IMPORTED = [
        'GrahamTrucking' => self::ALL_COMPANIES['GrahamTrucking'],
        'Mercer' => self::ALL_COMPANIES['Mercer'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::TO_BE_IMPORTED as $companyName => $files) {
            $company = Company::where('name', $companyName)->first(['id', 'name']);
            foreach ($files as $importInformation) {
                $this->command->info('');
                $this->command->alert("Importing '{$importInformation['columns']['item_type']}' for company '{$company->name}'");

                $filePath = database_path($importInformation['file']);
                $rows = $this->readFile($filePath, $importInformation['sheet'] ?? null);

                DB::beginTransaction();
                $rows->each(function ($row, $rowNumber) use ($importInformation, $company, $rows) {
                    $dictionaryData = $this->extractDictionaryData($row, $importInformation['columns']);
                    $lookUpColumns = [
                                't_company_id' => $company->id,
                                'item_type' => $dictionaryData['item_type'],
                                'item_key' => $dictionaryData['item_key'],
                            ];

                    $item = DictionaryItem::updateOrCreate($lookUpColumns, $dictionaryData);

                    $status = $item->wasRecentlyCreated ? 'created' : 'updated';

                    $message = "{$status} datafile row: {$rowNumber}/{$rows->count()}, item_key: '{$dictionaryData['item_key']}', item_display_name: '{$dictionaryData['item_display_name']}'";
                    $this->command->info($message);
                });
                DB::commit();
            }
        }
    }

    protected function readFile(string $path, ?int $sheet): Collection
    {
        return tap(new FastExcel(), function ($fileReader) use ($sheet) {
            return $sheet === null ? $fileReader : $fileReader->sheet($sheet);
        })
        ->import($path)
        ->reject(function ($row) {
            return trim(implode('', $row)) == '';
        });
    }

    protected function extractDictionaryData(array $row, array $columnsMap): array
    {
        return [
            'item_type' => $columnsMap['item_type'],
            'item_key' => $row[$columnsMap['item_key']],
            'item_display_name' => $row[$columnsMap['item_display_name']],
            'item_value' => collect($columnsMap['item_value'])
                ->map(fn ($rowColumn) => $row[$rowColumn])
                ->toArray(),
        ];
    }
}
