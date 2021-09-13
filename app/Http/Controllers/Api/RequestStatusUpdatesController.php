<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\OCRRequestStatus;
use App\Events\RequestStatusUpdated;
use App\Http\Controllers\Controller;

class RequestStatusUpdatesController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'token' => ['required', 'alpha_dash'],
            'request_id' => ['required', 'exists:t_job_latest_state,request_id'],
            'status' => ['required', 'string'],
            'status_date' => ['required'],
            'status_metadata' => ['required', 'array'],
            'company_id' => ['required', 'integer'],
            'order_id' => ['nullable'],
        ]);

        abort_if(
            $data['token'] != config('services.dray360-api.webhook_key'),
            Response::HTTP_UNAUTHORIZED,
            'Unauthorized'
        );

        unset($data['token']);
        $data['display_status'] = OCRRequestStatus::STATUS_MAP[$data['status']] ?? '-';

        broadcast(new RequestStatusUpdated($data))->toOthers();
    }
}
