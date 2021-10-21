<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;
use App\Http\Controllers\Controller;
use App\Queries\AuditLogsOrderPropertyQuery;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderPropertiesAuditLogsController extends Controller
{
    public function __invoke(Request $request)
    {
        $this->authorize('viewAny', Audit::class);

        $filters = $request->validate([
            'property' => 'required|string',
            'time_range' => 'sometimes|integer',
            'start_date' => 'required_with:end_date|date',
            'end_date' => 'required_with:start_date|date|after_or_equal:start_date',
            'per_page' => 'sometimes|nullable|integer'
        ]);

        return JsonResource::collection(
            (new AuditLogsOrderPropertyQuery($filters))
                ->paginate($filters['per_page'] ?? null)
        );
    }
}
