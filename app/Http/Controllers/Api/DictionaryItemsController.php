<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\DictionaryItem;
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
            ->allowedFilters([
                AllowedFilter::exact('company_id', 't_company_id'),
                AllowedFilter::exact('tms_provider_id', 't_tms_provider_id'),
                AllowedFilter::exact('user_id', 't_user_id'),
                AllowedFilter::exact('item_type'),
            ])
            ->get();

        return JsonResource::collection($dictionaryItems);
    }
}
