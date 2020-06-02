<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\AccountOCRVariantOCRRule;
use App\Http\Resources\OCRRule as ResourcesOCRRule;

class OCRRulesAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'account_id' => 'required',
            'variant_id' => 'required',
        ]);

        return new ResourcesOCRRule(
            AccountOCRVariantOCRRule::assignedTo($filters['account_id'], $filters['variant_id'])
                ->with('ocrRule')
                ->orderBy('rule_sequence')
                ->get()
                ->pluck('ocrRule')
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'account_id' => 'required',
            'variant_id' => 'required',
            'rules' => 'required|array',
            'rules.*' => 'integer|exists:t_ocrrules,id',
        ]);
        $accountId = $data['account_id'];
        $variantId = $data['variant_id'];

        $data = collect($data['rules'])->map(function ($rule, $key) use ($accountId, $variantId) {
            return [
                't_account_id' => $accountId,
                't_ocrvariant_id' => $variantId,
                't_ocrrule_id' => $rule,
                'rule_sequence' => $key + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        AccountOCRVariantOCRRule::assignedTo($accountId, $variantId)->delete();
        if (!AccountOCRVariantOCRRule::insert($data)) {
            abort(500, 'There was an error trying to save the assignment');
        }

        return response()->json(['data' => $data], Response::HTTP_CREATED);
    }
}
