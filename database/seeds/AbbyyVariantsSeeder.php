<?php

/**
 * Usage: php artisan db:seed --class=OCRVariantsSeeder
 */

use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * TerminalSeeder builds tables from Abbyy's sqllite3 database extract of all variants
 *
 * t_ocrvariants
 * =============
 * id
 * abbyy_variant_id
 * abbyy_variant_name
 * shipment_type
 * zero
 */
class OCRVariantsSeeder extends Seeder
{
    const INPUT_FILES = [
        [
            "FILENAME" => 'database/seeds/abbyy_variants_20201218.csv'
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::INPUT_FILES as $inputfile) {
            $filename = $inputfile['FILENAME'];
            $variantsData = $this->getCsvData($filename);
            $rowCount = count($variantsData);

            foreach ($variantsData as $csvRow) {
                $variantRow = $this->getVariantRowFromCsvRow($csvRow);
                $abbyyVariantId = $variantRow['abbyy_variant_id'];
                $abbyyVariantName = $variantRow['abbyy_variant_name'];
                $exists = $this->variantExists($abbyyVariantId, $abbyyVariantName);
                if (! $exists) {
                    $this->insertRow($rowCount, $variantRow);
                } else {
                    $this->updateRow($rowCount, $variantRow);
                }
            }
        }
    }

    /**
     * Load the CSV file, return array. Note that this is shameless
     * copy/pasta from the EquipmentLeaseTypeSeeder.
     *
     * TODO: make this a  shared library somewhere.
     *
     * @param string $filename name of csv file
     *
     * @return array
     */
    public function getCsvData($filename)
    {
        print("\n");
        print("\n");
        print("\n");
        print('reading: '.$filename."\n");
        print("\n");

        $alldata = [];
        if (($handle = fopen($filename, "r")) !== false) {
            $headers = null;
            $rownum = -1;
            while (($data = fgetcsv($handle)) !== false) {
                $rownum++;
                if ($rownum == 0) {
                    $headers = $data;
                } else {
                    $rowData = ['rownum' => $rownum];
                    $fieldcount = count($data);
                    for ($i = 0; $i < $fieldcount; $i++) {
                        $colname = $headers[$i];
                        $coldata = $data[$i];
                        $rowData[$colname] = $coldata;
                    }
                    array_push($alldata, $rowData);
                }
            }
            fclose($handle);
        }
        return $alldata;
    }

    /**
     * Parse one row of ABBYY's CSV variants data
     * Assumes file format: "id","abbyy_variant_id","abbyy_variant_name","shipment_type","zero"
     *
     * @param $csvRow full row data
     *
     * @return EquipmentType
     */
    public function getVariantRowFromCsvRow($csvRow)
    {
        return ([
            'abbyy_variant_id' => $csvRow['abbyy_variant_id'],
            'abbyy_variant_name' => $csvRow['abbyy_variant_name'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    /**
     * Return true if row already exists for company/tms/equipid (and is not soft-deleted)
     *
     * @param $abbyyVariantId   id
     * @param $abbyyVariantName name
     *
     * @return boolean exists
     */
    public function variantExists($abbyyVariantId, $abbyyVariantName)
    {
        $rows = DB::table('t_ocrvariants')
            ->where(
                [
                    ['abbyy_variant_id', '=', $abbyyVariantId],
                    ['abbyy_variant_name', '=', $abbyyVariantName],
                ]
            )
            ->whereNull('deleted_at')  // don't delete a row that is already soft-deleted
            ->get();
        $exists = (count($rows) > 0);
        return $exists;
    }

    /**
     * Insert row into t_equipment_types database
     *
     * @param $rowCount         row number being inserted
     * @param $equipmentTypeRow a single row of data
     *
     * @return int new row id
     */
    public function insertRow($rowCount, $variantRow)
    {
        $v = $variantRow;
        // insert the address
        $variantId = DB::table('t_ocrvariants')
        ->insertGetId(
            [
                'abbyy_variant_id' => $v['abbyy_variant_id'],
                'abbyy_variant_name' => $v['abbyy_variant_name'],
                'created_at' => $v['created_at'],
                'updated_at' => $v['updated_at'],
            ]
        );

        // happy message, return
        $msg = 'inserted csvfile row:'.$v['rownum'].'/'.$rowCount.' for variant: "'.$v['abbyy_variant_name'].'" as t_ocrvariants.id: '.$variantId."\n";
        print($msg);
        return ($variantId);
    }

    /**
     * Update existing row for company/tms/equipid
     *
     * @param $rowCount   row number being inserted
     * @param $variantRow a single row of data
     *
     * @return void
     */
    public function updateRow($rowCount, $variantRow)
    {
        $v = $variantRow;
        $abbyyVariantId = $v['abbyy_variant_id'];
        $abbyyVariantId = $v['abbyy_variant_name'];
        // insert the address
        $updatedRows = DB::table('t_ocrvariants')
            ->where(
                [
                    ['abbyy_variant_id', '=', $abbyyVariantId],
                    ['abbyy_variant_name', '=', $abbyyVariantName],
                ]
            )
            ->whereNull('deleted_at')
            ->update(
                [
                    'abbyy_variant_id' => $v['abbyy_variant_id'],
                    'abbyy_variant_name' => $v['abbyy_variant_name'],
                    'updated_at' => $e['updated_at'],
                ]
            );

        // happy message, return
        $msg = 'updated csvfile row:'.$v['rownum'].'/'.$rowCount.' for variant: "'.$v['abbyy_variant_name'].'\n';
        print($msg);
    }
}
