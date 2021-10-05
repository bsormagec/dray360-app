<?php

namespace App\Queries;

use App\Models\Order;
use App\Models\OCRRequest;
use Illuminate\Support\Carbon;
use App\Models\FeedbackComment;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class FeedbacksListQuery extends QueryBuilder
{
    public function __construct(array $filters)
    {
        $query = FeedbackComment::query()
            ->select([
                't_feedback_comments.*',
                'c.name as company_name',
                'c.id as company_id',
            ])
            ->with([
                'user:id,name',
                'user.roles:id,name',
            ])
            ->with(['commentable' => fn ($q) => $q->select(['id', 'request_id', 'deleted_at'])->withTrashed()])
            ->join('users', 'users.id', '=', 'user_id')
            ->leftJoin('t_orders as o', function ($join) {
                $join->on('o.id', '=', 't_feedback_comments.commentable_id')
                    ->where('t_feedback_comments.commentable_type', Order::class);
            })
            ->leftJoin('t_job_latest_state as lsr', function ($join) {
                $join->on('lsr.id', '=', 't_feedback_comments.commentable_id')
                    ->where('t_feedback_comments.commentable_type', OCRRequest::class);
            })
            ->leftJoin('t_job_state_changes as sc', function ($join) {
                $join->on('sc.id', '=', 'lsr.t_job_state_changes_id')
                    ->where('t_feedback_comments.commentable_type', OCRRequest::class);
            })
            ->leftJoin('t_companies as c', function ($join) {
                $join->on('c.id', '=', 'o.t_company_id')->orWhereColumn('c.id', 'sc.company_id');
            });

        parent::__construct($query);

        $this->allowedFilters([
            AllowedFilter::callback('company_id', function ($query, $value) use ($filters) {
                $method = is_array($value) ? 'whereIn' : 'where';

                return $query->{$method}('c.id', $value);
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
            AllowedFilter::callback('role', function ($query, $value) {
                return $query->whereHas('user', fn ($q) => $q->whereRoleIs($value));
            }),
            AllowedFilter::callback('commentable_type', function ($query, $value) {
                return $query->where('commentable_type', FeedbackComment::CLASSES_MAP[$value]);
            }),
            AllowedFilter::callback('start_date', function ($query, $value) {
                $date = Carbon::createFromDate($value)->startOfDay()->toDateTimeString();
                return $query->where('t_feedback_comments.created_at', '>=', $date);
            }),
            AllowedFilter::callback('end_date', function ($query, $value) {
                $date = Carbon::createFromDate($value)->endOfDay()->toDateTimeString();
                return $query->where('t_feedback_comments.created_at', '<=', $date);
            }),
            AllowedFilter::exact('user_id'),
            AllowedFilter::partial('comment'),
        ])
        ->defaultSort('-t_feedback_comments.created_at', '-t_feedback_comments.id')
        ->allowedSorts([
            AllowedSort::field('id', 't_feedback_comments.id'),
            AllowedSort::field('commentable_id', 't_feedback_comments.commentable_id'),
            AllowedSort::field('commentable_type', 't_feedback_comments.commentable_type'),
            AllowedSort::field('created_at', 't_feedback_comments.created_at'),
            AllowedSort::field('updated_at', 't_feedback_comments.updated_at'),
            AllowedSort::field('order_id', 'o.id'),
            AllowedSort::field('company', 'company_name'),
            AllowedSort::field('comment', 't_feedback_comments.comment'),
            AllowedSort::field('user', 'users.name'),
        ]);
    }
}
