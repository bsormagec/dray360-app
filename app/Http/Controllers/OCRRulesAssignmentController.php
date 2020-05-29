<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\AccountOCRVariantOCRRule;

class OCRRulesAssignmentController extends Controller
{
    public function __invoke(Request $request)
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
            ];
        })->toArray();

        AccountOCRVariantOCRRule::assignedTo($accountId, $variantId)->delete();
        if (!AccountOCRVariantOCRRule::insert($data)) {
            abort(500, 'There was an error trying to save the assignment');
        }

        return response()->json($data, Response::HTTP_CREATED);
    }
}
