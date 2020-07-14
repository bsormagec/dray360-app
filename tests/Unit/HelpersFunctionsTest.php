<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HelpersFunctionsTest extends TestCase
{
    /** @test */
    public function it_should_extract_the_correct_file_name_from_s3_url()
    {
        $fileUrl = "s3://dmedocproc-ocrprocessedjobs-dev/e061c2fb-7ec3-596b-acdc-95b461f27e2b_daedc679e9e9b441d98c0634e83ae24bab8722a385058db6f9e08f545e770f1d_ShipmentCartageAdviceWithReceipt-SSI100072169.00000001.jpg";
        $filePath = "e061c2fb-7ec3-596b-acdc-95b461f27e2b_daedc679e9e9b441d98c0634e83ae24bab8722a385058db6f9e08f545e770f1d_ShipmentCartageAdviceWithReceipt-SSI100072169.00000001.jpg";
        $this->assertEquals($filePath, s3_file_name_from_url($fileUrl));

        $fileUrl = "s3://dmedocproc-emailintake-dev/intakearchive/1fa83bf8-3c64-5db5-a12e-6c96dc61269d_9f34ffd1b9ba31db17de0b21d6f4028f7f4191ac170ae9ee53dd86f3f7cb3529_ShipmentCartageAdviceWithReceipt-SSI100072107.PDF";
        $filePath = "intakearchive/1fa83bf8-3c64-5db5-a12e-6c96dc61269d_9f34ffd1b9ba31db17de0b21d6f4028f7f4191ac170ae9ee53dd86f3f7cb3529_ShipmentCartageAdviceWithReceipt-SSI100072107.PDF";
        $this->assertEquals($filePath, s3_file_name_from_url($fileUrl));
    }

    /** @test */
    public function it_should_get_the_bucket_from_a_s3_url()
    {
        $fileUrl = "s3://dmedocproc-ocrprocessedjobs-dev/e061c2fb-7ec3-596b-acdc-95b461f27e2b_daedc679e9e9b441d98c0634e83ae24bab8722a385058db6f9e08f545e770f1d_ShipmentCartageAdviceWithReceipt-SSI100072169.00000001.jpg";
        $bucket = 'dmedocproc-ocrprocessedjobs-dev';
        $this->assertEquals($bucket, s3_bucket_from_url($fileUrl));

        $fileUrl = "s3://dmedocproc-emailintake-dev/intakearchive/1fa83bf8-3c64-5db5-a12e-6c96dc61269d_9f34ffd1b9ba31db17de0b21d6f4028f7f4191ac170ae9ee53dd86f3f7cb3529_ShipmentCartageAdviceWithReceipt-SSI100072107.PDF";
        $bucket = 'dmedocproc-emailintake-dev';
        $this->assertEquals($bucket, s3_bucket_from_url($fileUrl));
    }
}
