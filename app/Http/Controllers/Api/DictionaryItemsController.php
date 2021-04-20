<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\DictionaryItem;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Http\Resources\Json\JsonResource;

class DictionaryItemsController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', DictionaryItem::class);
        $dictionaryItems = QueryBuilder::for(DictionaryItem::class)
            ->allowedSorts(['item_display_name', 'item_key'])
            ->defaultSort('item_display_name')
            ->allowedFilters([
                AllowedFilter::exact('company_id', 't_company_id'),
                AllowedFilter::exact('tms_provider_id', 't_tms_provider_id'),
                AllowedFilter::exact('user_id', 't_user_id'),
                AllowedFilter::exact('item_type'),
            ])
            ->get();

        return JsonResource::collection($dictionaryItems);
    }

    public function store(Request $request)
    {
        $this->authorize('create', DictionaryItem::class);

        $data = $request->validate([
            'item_key' => 'required|string',
            'item_display_name' => 'required|string',
            'item_value' => 'sometimes|nullable',
            'item_type' => ['required', Rule::in(DictionaryItem::TYPES_LIST)],
            't_company_id' => 'required_without_all:t_tms_provider_id,t_user_id|exists:t_companies,id',
            't_tms_provider_id' => 'required_without_all:t_company_id,t_user_id|exists:t_tms_providers,id',
            't_user_id' => 'required_without_all:t_company_id,t_tms_provider_id|exists:users,id',
        ]);

        return DictionaryItem::create($data);
    }
}
