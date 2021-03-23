<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\ObjectLock;
use App\Models\OCRRequest;
use Illuminate\Http\Response;
use Tests\Seeds\OcrRequestSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ObjectLocksControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected OCRRequest $ocrRequest;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginAdmin();
        (new OcrRequestSeeder())->seedOcrJob_ocrWaiting();
        $this->ocrRequest = OCRRequest::orderByDesc('id')->first();
    }

    /** @test */
    public function it_should_create_the_lock_for_the_given_object()
    {
        $data = [
            'object_type' => ObjectLock::REQUEST_OBJECT,
            'object_id' => $this->ocrRequest->request_id,
            'lock_type' => ObjectLock::SELECT_REQUEST_TYPE,
        ];
        $this->post(route('object-locks.store'), $data)
        ->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseCount('t_object_locks', 1);
        $this->assertDatabaseHas('t_object_locks', $data + ['user_id' => auth()->id()]);
    }

    /** @test */
    public function it_should_not_fail_if_same_user_tries_to_get_lock_again()
    {
        $data = [
            'object_type' => ObjectLock::REQUEST_OBJECT,
            'object_id' => $this->ocrRequest->request_id,
            'lock_type' => ObjectLock::SELECT_REQUEST_TYPE,
            'user_id' => auth()->id(),
        ];
        ObjectLock::create($data);

        $this->post(route('object-locks.store'), $data)->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseCount('t_object_locks', 1);
    }

    /** @test */
    public function it_should_fail_if_a_lock_is_already_created_and_other_user_tries_to_get_it()
    {
        $data = [
            'object_type' => ObjectLock::REQUEST_OBJECT,
            'object_id' => $this->ocrRequest->request_id,
            'lock_type' => ObjectLock::SELECT_REQUEST_TYPE,
            'user_id' => auth()->id(),
        ];
        ObjectLock::create($data);
        $this->loginCustomerAdmin();
        auth()->user()->attachPermissions(['object-locks-create', 'object-locks-edit']);

        $this->post(route('object-locks.store'), $data)->assertStatus(Response::HTTP_CONFLICT);

        $this->assertDatabaseCount('t_object_locks', 1);
    }

    /** @test */
    public function it_should_delete_old_lock_and_create_new_one_if_its_claim_lock()
    {
        Carbon::setTestNow(now()->subSeconds(10));
        $oldData = [
            'object_type' => ObjectLock::REQUEST_OBJECT,
            'object_id' => $this->ocrRequest->request_id,
            'lock_type' => ObjectLock::SELECT_REQUEST_TYPE,
            'user_id' => auth()->id(),
        ];
        ObjectLock::create($oldData);
        Carbon::setTestNow(now()->addSeconds(10));

        $data = [
            'object_type' => ObjectLock::REQUEST_OBJECT,
            'object_id' => $this->ocrRequest->request_id,
            'lock_type' => ObjectLock::CLAIM_LOCK_TYPE,
        ];
        $this->post(route('object-locks.store'), $data)->assertStatus(Response::HTTP_CREATED);
        $this->assertSoftDeleted('t_object_locks', $oldData);
        $this->assertEquals(1, ObjectLock::count());
        $this->assertDatabaseHas('t_object_locks', $data + ['user_id' => auth()->id()]);
    }

    /** @test */
    public function it_should_refresh_the_current_active_lock()
    {
        Carbon::setTestNow(now()->subSeconds(15));
        $data = [
            'object_type' => ObjectLock::REQUEST_OBJECT,
            'object_id' => $this->ocrRequest->request_id,
            'lock_type' => ObjectLock::SELECT_REQUEST_TYPE,
            'user_id' => auth()->id(),
        ];
        ObjectLock::create($data);
        Carbon::setTestNow(now()->addSeconds(15));

        $this->put(route('object-locks.update'), $data)->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseCount('t_object_locks', 1);
    }

    /** @test */
    public function it_should_fail_to_refresh_lock_if_it_expired()
    {
        Carbon::setTestNow(now()->subSeconds(ObjectLock::REFRESH_INTERVAL_SECONDS + 2));
        $data = [
            'object_type' => ObjectLock::REQUEST_OBJECT,
            'object_id' => $this->ocrRequest->request_id,
            'lock_type' => ObjectLock::SELECT_REQUEST_TYPE,
            'user_id' => auth()->id(),
        ];
        ObjectLock::create($data);
        Carbon::setTestNow(now()->addSeconds(ObjectLock::REFRESH_INTERVAL_SECONDS + 2));

        $this->put(route('object-locks.update'), $data)->assertStatus(Response::HTTP_CONFLICT);
        $this->assertSoftDeleted('t_object_locks', $data);
    }

    /** @test */
    public function it_should_delete_the_lock_for_a_given_object()
    {
        Carbon::setTestNow(now()->subSeconds(ObjectLock::REFRESH_INTERVAL_SECONDS + 2));
        $data = [
            'object_type' => ObjectLock::REQUEST_OBJECT,
            'object_id' => $this->ocrRequest->request_id,
            'lock_type' => ObjectLock::SELECT_REQUEST_TYPE,
            'user_id' => auth()->id(),
        ];
        ObjectLock::create($data);
        Carbon::setTestNow(now()->addSeconds(ObjectLock::REFRESH_INTERVAL_SECONDS + 2));

        $this->delete(route('object-locks.destroy'), $data)->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertSoftDeleted('t_object_locks', $data);
    }

    /** @test */
    public function it_should_fail_if_you_try_to_delete_lock_that_user_doesnt_have()
    {
        $anotherUser = User::where('id', '!=', auth()->id())->first();
        $data = [
            'object_type' => ObjectLock::REQUEST_OBJECT,
            'object_id' => $this->ocrRequest->request_id,
            'lock_type' => ObjectLock::SELECT_REQUEST_TYPE,
            'user_id' => $anotherUser->id,
        ];
        ObjectLock::create($data);

        $this->delete(route('object-locks.destroy'), $data)->assertStatus(Response::HTTP_CONFLICT);
    }
}
