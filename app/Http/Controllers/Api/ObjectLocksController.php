<?php

namespace App\Http\Controllers\Api;

use App\Models\ObjectLock;
use App\Events\ObjectLocked;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Events\ObjectUnlocked;
use App\Http\Controllers\Controller;

class ObjectLocksController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('create', ObjectLock::class);
        $data = $request->validate(ObjectLock::rules());

        if (
            $data['lock_type'] == ObjectLock::CLAIM_LOCK_TYPE
            && auth()->user()->isAbleTo('object-locks-edit')
        ) {
            $currentLock = ObjectLock::findFor($data['object_type'], $data['object_id']);
            if ($currentLock) {
                $currentLock->delete();
            }
        } elseif (ObjectLock::existsActiveLock($data['object_type'], $data['object_id'], auth()->id())) {
            return ObjectLock::findFor($data['object_type'], $data['object_id'], auth()->id());
        } elseif (ObjectLock::existsActiveLock($data['object_type'], $data['object_id'])) {
            return response()->json(['data' => [
                'status' => 'error',
                'message' => 'The object has been already locked'
            ]], Response::HTTP_CONFLICT);
        }

        $lock = ObjectLock::create($data + ['user_id' => auth()->id()]);
        broadcast(new ObjectLocked($lock))->toOthers();

        return $lock;
    }

    public function update(Request $request)
    {
        $rules = ObjectLock::rules();
        unset($rules['lock_type']);
        $data = $request->validate($rules);
        $lock = ObjectLock::findFor($data['object_type'], $data['object_id'], auth()->id());

        if (! $lock) {
            return response()->json(['data' => [
                'status' => 'error',
                'message' => 'You do not have an active lock'
            ]], Response::HTTP_CONFLICT);
        }

        if (! $lock->isActive()) {
            $lock->delete();
            return response()->json(['data' => [
                'status' => 'error',
                'message' => 'You do not have an active lock'
            ]], Response::HTTP_CONFLICT);
        }

        $lock->touch();

        return response()->noContent();
    }

    public function destroy(Request $request)
    {
        $rules = ObjectLock::rules();
        unset($rules['lock_type']);
        $data = $request->validate($rules);
        $lock = ObjectLock::findFor($data['object_type'], $data['object_id'], auth()->id());

        if (! $lock) {
            return response()->json(['data' => [
                'status' => 'error',
                'message' => 'You do not have an active lock'
            ]], Response::HTTP_CONFLICT);
        }

        $lock->delete();
        broadcast(new ObjectUnlocked($lock))->toOthers();

        return response()->noContent();
    }
}
