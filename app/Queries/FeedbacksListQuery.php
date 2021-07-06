<?php

namespace App\Queries;

use App\Models\OCRRequest;
use App\Models\FeedbackComment;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class FeedbacksListQuery extends QueryBuilder
{
    public function __construct(array $filters)
    {
        $query = FeedbackComment::query()
            ->select(['t_feedback_comments.*'])
            ->with([
                'user:id,name',
                'user.roles:id,name',
            ]);

        parent::__construct($query);

        $this->allowedFilters([
            AllowedFilter::callback('company_id', function ($query, $value) use ($filters) {
                $class = FeedbackComment::CLASSES_MAP[$filters['filter']['commentable_type']];

                if ($class == OCRRequest::class) {
                    return $query->join('t_job_latest_state', 't_job_latest_state.id', '=', 'commentable_id')
                        ->join('t_job_state_changes', 't_job_state_changes.id', '=', 't_job_state_changes_id')
                        ->when(
                            is_array($value),
                            fn ($query) => $query->whereIn('company_id', $value),
                            fn ($query) => $query->where('company_id', $value)
                        );
                }

                return $query->join('t_orders', 't_orders.id', '=', 'commentable_id')
                    ->when(
                        is_array($value),
                        fn ($query) => $query->whereIn('t_company_id', $value),
                        fn ($query) => $query->where('t_company_id', $value)
                    );
            }),
            AllowedFilter::callback('commentable_id', function ($query, $value) use ($filters) {
                $class = FeedbackComment::CLASSES_MAP[$filters['filter']['commentable_type']];

                if ($class == OCRRequest::class) {
                    return $query->join('t_job_latest_state', function ($join) use ($value) {
                        $join->on('t_job_latest_state.id', '=', 'commentable_id')
                            ->whereNull('order_id')
                            ->where('request_id', 'like', "{$value}%");
                    });
                }

                return $query->where('commentable_id', $value);
            }),
            AllowedFilter::callback('commentable_type', function ($query, $value) {
                return $query->where('commentable_type', FeedbackComment::CLASSES_MAP[$value]);
            }),
            AllowedFilter::callback('start_date', function ($query, $value) {
                return $query->whereDate('t_feedback_comments.created_at', '>=', $value);
            }),
            AllowedFilter::callback('end_date', function ($query, $value) {
                return $query->whereDate('t_feedback_comments.created_at', '<=', $value);
            }),
        ])
        ->defaultSort('-t_feedback_comments.created_at', '-t_feedback_comments.id')
        ->allowedSorts([
            AllowedSort::field('id', 't_feedback_comments.id'),
            AllowedSort::field('commentable_id', 't_feedback_comments.commentable_id'),
            AllowedSort::field('commentable_type', 't_feedback_comments.commentable_type'),
            AllowedSort::field('created_at', 't_feedback_comments.created_at'),
            AllowedSort::field('updated_at', 't_feedback_comments.updated_at'),
        ]);
    }
}
