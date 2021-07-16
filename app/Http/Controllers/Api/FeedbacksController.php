<?php

namespace App\Http\Controllers\Api;

use App\Models\OCRRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\FeedbackComment;
use Illuminate\Validation\Rule;
use App\Queries\FeedbacksListQuery;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedbacksController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', FeedbackComment::class);

        $request->validate([
            'filter.commentable_type' => ['required_with:filter.commentable_id,filter.company_id', Rule::in(array_keys(FeedbackComment::CLASSES_MAP))],
            'filter.start_date' => ['date', 'required_with:filter.end_date'],
            'filter.end_date' => ['after_or_equal:filter.start_date'],
        ]);

        return JsonResource::collection(
            (new FeedbacksListQuery($request->all()))->paginate($request->get('per_page', 10))
        );
    }

    public function store(Request $request)
    {
        $this->authorize('create', FeedbackComment::class);
        $data = $request->validate([
            'comment' => ['required', 'string'],
            'commentable_type' => ['required', Rule::in(array_keys(FeedbackComment::CLASSES_MAP))],
            'commentable_id' => ['required'],
        ]);

        abort_if(
            ! isset(FeedbackComment::CLASSES_MAP[$data['commentable_type']]),
            404,
            'Commentable type not found'
        );

        $commentableObject = $this->getCommentableObject($data['commentable_type'], $data['commentable_id']);
        $comment = $commentableObject->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $data['comment'],
        ]);

        return response()->json($comment, Response::HTTP_CREATED);
    }

    protected function getCommentableObject($type, $id)
    {
        $class = FeedbackComment::CLASSES_MAP[$type];

        if ($class == OCRRequest::class) {
            return $class::where('request_id', $id)->first(['id', 'request_id']);
        }

        return $class::find($id, ['id']);
    }
}
