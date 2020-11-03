<?php

namespace App\Http\Controllers\Api;

use App\Models\OCRRule;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\CompanyOCRVariantOCRRule;
use App\Http\Resources\OCRRule as ResourcesOCRRule;

class OCRRulesAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', OCRRule::class);
        $filters = $request->validate([
            'company_id' => 'required|integer',
            'variant_id' => 'required|integer',
        ]);

        return new ResourcesOCRRule(
            CompanyOCRVariantOCRRule::assignedTo($filters['company_id'], $filters['variant_id'])
                ->has('ocrRule')
                ->with('ocrRule')
                ->orderBy('rule_sequence')
                ->get()
                ->pluck('ocrRule')
        );
    }

    public function store(Request $request)
    {
        $this->authorize('assign', OCRRule::class);
        $data = $request->validate([
            'company_id' => 'required|integer|exists:t_companies,id',
            'variant_id' => 'required|integer|exists:t_ocrvariants,id',
            'rules' => 'present|array',
            'rules.*' => 'integer|exists:t_ocrrules,id',
        ]);
        $companyId = $data['company_id'];
        $variantId = $data['variant_id'];

        $data = collect($data['rules'])->map(function ($rule, $key) use ($companyId, $variantId) {
            return [
                't_company_id' => $companyId,
                't_ocrvariant_id' => $variantId,
                't_ocrrule_id' => $rule,
                'rule_sequence' => $key + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        CompanyOCRVariantOCRRule::assignedTo($companyId, $variantId)->delete();

        if (! CompanyOCRVariantOCRRule::insert($data)) {
            abort(500, 'There was an error trying to save the assignment');
        }

        return response()->json(['data' => $data], Response::HTTP_CREATED);
    }
}
