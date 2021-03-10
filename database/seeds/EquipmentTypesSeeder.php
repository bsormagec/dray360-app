<?php

/**
 * Usage: php artisan db:seed --class=EquipmentTypesSeeder
 */

use App\Models\Company;
use App\Models\EquipmentType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

class EquipmentTypesSeeder extends Seeder
{
    const TO_BE_IMPORTED = [
        'Cushing' => [
            'file' => 'seeds/cushing_equipment_lease_types_20201217.csv',
            'columns' => [
                'tms_equipment_id' => 'id',
                'equipment_owner' => 'line',
                'equipment_type_and_size' => 'type',
                'equipment_size' => 'equipmentlength',
                'scac' => 'scac',
                'line_prefix_list' => 'lineprefix',
            ],
        ],
        'TCompaniesDemo' => [
            'file' => 'seeds/cushing_equipment_lease_types_20201217.csv',
            'columns' => [
                'tms_equipment_id' => 'id',
                'equipment_owner' => 'line',
                'equipment_type_and_size' => 'type',
                'equipment_size' => 'equipmentlength',
                'scac' => 'scac',
                'line_prefix_list' => 'lineprefix',
            ],
        ],
        // 'IXTOnboarding' => [
        //     'file' => 'seeds/ixt_equipment_lease_types_20201028.csv',
        //     'columns' => [
        //         'tms_equipment_id' => 'id',
        //         'equipment_owner' => 'line',
        //         'equipment_type_and_size' => 'type',
        //         'equipment_size' => 'equipmentlength',
        //         'scac' => 'scac',
        //         'line_prefix_list' => 'lineprefix',
        //     ],
        // ],
        // 'IXT' => [
        //     'file' => 'seeds/ixt_equipment_lease_types_20201028.csv',
        //     'columns' => [
        //         'tms_equipment_id' => 'id',
        //         'equipment_owner' => 'line',
        //         'equipment_type_and_size' => 'type',
        //         'equipment_size' => 'equipmentlength',
        //         'scac' => 'scac',
        //         'line_prefix_list' => 'lineprefix',
        //     ],
        // ],
        'PortCityLogisticsOnboarding' => [
            'file' => 'seeds/pcl_equipment_lease_types_20201028.csv',
            'columns' => [
                'tms_equipment_id' => 'id',
                'equipment_owner' => 'line',
                'equipment_type_and_size' => 'type',
                'equipment_size' => 'equipmentlength',
                'scac' => 'scac',
                'line_prefix_list' => 'lineprefix',
            ],
        ],
        'PortCityLogistics' => [
            'file' => 'seeds/pcl_equipment_lease_types_20201028.csv',
            'columns' => [
                'tms_equipment_id' => 'id',
                'equipment_owner' => 'line',
                'equipment_type_and_size' => 'type',
                'equipment_size' => 'equipmentlength',
                'scac' => 'scac',
                'line_prefix_list' => 'lineprefix',
            ],
        ],
        'Zariz' => [
            'file' => 'seeds/Zariz Equipment Lease Types.xlsx',
            'sheet' => 1,
            'columns' => [
                'tms_equipment_id' => 'id',
                'equipment_owner' => 'line',
                'equipment_type_and_size' => 'type',
                'equipment_size' => 'equipmentlength',
                'scac' => 'scac',
                'line_prefix_list' => 'lineprefix',
            ],
        ],
        'ZarizOnboarding' => [
            'file' => 'seeds/Zariz Equipment Lease Types.xlsx',
            'sheet' => 1,
            'columns' => [
                'tms_equipment_id' => 'id',
                'equipment_owner' => 'line',
                'equipment_type_and_size' => 'type',
                'equipment_size' => 'equipmentlength',
                'scac' => 'scac',
                'line_prefix_list' => 'lineprefix',
            ],
        ],
        'WBContainer' => [
            'file' => 'seeds/WBContainer_equipment_types_20210215.xlsx',
            'sheet' => 1,
            'columns' => [
                'tms_equipment_id' => 'id',
                'equipment_owner' => 'line',
                'equipment_type_and_size' => 'type',
                'equipment_size' => 'equipmentlength',
                'scac' => 'scac',
                'line_prefix_list' => 'lineprefix',
            ],
        ],
        'WBContainerOnboarding' => [
            'file' => 'seeds/WBContainer_equipment_types_20210215.xlsx',
            'sheet' => 1,
            'columns' => [
                'tms_equipment_id' => 'id',
                'equipment_owner' => 'line',
                'equipment_type_and_size' => 'type',
                'equipment_size' => 'equipmentlength',
                'scac' => 'scac',
                'line_prefix_list' => 'lineprefix',
            ],
        ],


        

    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::TO_BE_IMPORTED as $companyName => $importInformation) {
            $company = Company::where('name', $companyName)->firstOrFail(['id', 'name', 'default_tms_provider_id']);

            $this->command->info('');
            $this->command->alert("Importing equipment type for company '{$company->name}'");

            $filePath = database_path($importInformation['file']);
            $rows = $this->readFile($filePath, $importInformation['sheet'] ?? null);

            DB::beginTransaction();
            $rows->each(function ($row, $index) use ($importInformation, $company, $rows) {
                $equipmentTypeData = $this->extractEquipmentTypeData($row, $importInformation['columns']);
                $lookUpColumns = [
                        't_company_id' => $company->id,
                        't_tms_provider_id' => $company->default_tms_provider_id,
                        'tms_equipment_id' => $equipmentTypeData['tms_equipment_id'],
                    ];

                $item = EquipmentType::updateOrCreate($lookUpColumns, $equipmentTypeData);

                $status = $item->wasRecentlyCreated ? 'created' : 'updated';
                $rowNumber = $index + 1;

                $message = "{$status} datafile row: {$rowNumber}/{$rows->count()}, tms_equipment_id: '{$equipmentTypeData['tms_equipment_id']}'";
                $this->command->info($message);
            });
            DB::commit();
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

    protected function extractEquipmentTypeData(array $row, array $columnsMap): array
    {
        $sizeAndType = trim($row[$columnsMap['equipment_type_and_size']]);
        $size = trim($row[$columnsMap['equipment_size']]);
        $type = trim(str_replace($size, '', $sizeAndType));

        return [
            'tms_equipment_id' => trim($row[$columnsMap['tms_equipment_id']]),
            'equipment_owner' => trim($row[$columnsMap['equipment_owner']]),
            'row_type' => 'combined',
            'equipment_type_and_size' => $sizeAndType,
            'equipment_type' => $type,
            'equipment_size' => $size,
            'scac' => preg_replace("/[^A-Za-z0-9]/", '', $row[$columnsMap['equipment_owner']]),
            'line_prefix_list' => collect(explode(',', $row[$columnsMap['line_prefix_list']]))
                ->flatMap(fn ($item) => explode(' ', $item))
                ->map(fn ($item) => str_replace('\t', '', trim($item)))
                ->reject(fn ($item) => $item === '')
                ->values()
                ->toArray(),
        ];
    }
}
