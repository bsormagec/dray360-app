<?php

namespace Tests\Feature\Actions;

use Tests\TestCase;
use App\Models\ObjectLock;
use App\Models\DictionaryItem;
use App\Actions\GenerateUploadPresignedUrl;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GenerateUploadPresignedUrlTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_generate_a_presigned_url_for_uploading_content()
    {
        config()->set('filesystems.disks.s3.bucket', 'thebucket');
        $data = [
            'request_id' => 'someid',
            'filename' => 'image.jpg',
            'type' => DictionaryItem::PT_IMAGETYPE_TYPE,
        ];

        $response = (new GenerateUploadPresignedUrl())($data);

        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('upload_uri', $response['data']);
        $this->assertArrayHasKey('url_expiry_time', $response['data']);
        $this->assertArrayHasKey('uploading_filename', $response['data']);
        $this->assertStringContainsString('.apiupload', $response['data']['upload_uri']);
        $this->assertStringContainsString('imageupload/', $response['data']['upload_uri']);
    }

    /** @test */
    public function it_should_generate_a_presigned_url_for_uploading_ocr_request()
    {
        config()->set('filesystems.disks.s3.bucket', 'thebucket');
        $data = [
            'request_id' => 'someid',
            'filename' => 'image.jpg',
            'type' => ObjectLock::REQUEST_OBJECT,
        ];

        $response = (new GenerateUploadPresignedUrl())($data);

        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('upload_uri', $response['data']);
        $this->assertArrayHasKey('url_expiry_time', $response['data']);
        $this->assertArrayHasKey('uploading_filename', $response['data']);
        $this->assertStringContainsString('.apiupload', $response['data']['upload_uri']);
        $this->assertStringContainsString('intakeupload/', $response['data']['upload_uri']);
    }
}
