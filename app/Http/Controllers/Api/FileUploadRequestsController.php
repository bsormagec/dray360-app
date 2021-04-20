<?php

namespace App\Http\Controllers\Api;

use SplFileInfo;
use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DictionaryItem;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Actions\GenerateUploadPresignedUrl;
use Illuminate\Validation\ValidationException;

class FileUploadRequestsController extends Controller
{
    public function __invoke(Request $request)
    {
        // validate that filename parameter was provided
        $data = $request->validate([
            'filename' => ['required', 'string'],
            'request_id' => ['sometimes', 'nullable', Rule::exists('t_job_latest_state', 'request_id')],
            'order_id' => ['sometimes', 'nullable', Rule::exists('t_orders', 'id')],
            'type' => ['required', Rule::in([DictionaryItem::PT_IMAGETYPE_TYPE])],
        ]);

        $this->validateFileName($data['filename'], $data['type']);
        $this->validateCurrentCompany();

        $requestId = $this->generateRequestId($data);
        $newData = array_merge($data, ['request_id' => $requestId]);
        $response = app(GenerateUploadPresignedUrl::class)($newData);

        if ($response['status'] === 'error') {
            return response()->json($response, 500);
        }

        return [
            'data' => [
                'request_id' => $requestId,
                'original_filename' => $data['filename'],
                'datetime_utciso' => now()->toISOString(),
            ] + $response['data'],
        ];
    }

    protected function validateFileName($filename, $type)
    {
        $extension = strtolower((new SplFileInfo($filename))->getExtension());
        $extensionTypes = [
            DictionaryItem::PT_IMAGETYPE_TYPE => ['jpeg', 'jpg'],
        ];

        if (in_array($extension, $extensionTypes[$type])) {
            return;
        }

        $extensionList = implode(',', $extensionTypes[$type]);
        throw ValidationException::withMessages([
            'filename' => "Invalid file extension '{$extension}'. Must be one of: '{$extensionList}'",
        ]);
    }

    protected function validateCurrentCompany()
    {
        if (currentCompany()) {
            return;
        }

        throw ValidationException::withMessages([
            'company' => 'User not associated with a company can\'t upload files',
        ]);
    }

    protected function generateRequestId(array $data): string
    {
        if ($data['request_id'] ?? false) {
            return $data['request_id'];
        }

        if (isset($data['order_id']) ?? false) {
            $order = Order::find($data['order_id'], ['id', 'request_id']);

            return $order->request_id;
        }

        return Str::uuid()->toString();
    }
}
