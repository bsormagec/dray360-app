<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;

class BulkActionsController extends Controller
{
    private const actions = [
        'users' => [
            'activate' => \App\Actions\Bulk\ActivateUsers::class,
            'deactivate' => \App\Actions\Bulk\DeactivateUsers::class,
            'delete' => \App\Actions\Bulk\DeleteUsers::class,
            'reset_password' => \App\Actions\Bulk\ResetPasssword::class,
        ],
    ];

    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'models' => 'required|array',
            'models.*' => 'integer',
            'action' => 'required|in:'.$this->getAvailableActions()->implode(','),
        ]);

        $action = Arr::get(self::actions, $data['action']);

        return (new $action())($request, $data['models']);
    }

    protected function getAvailableActions(): Collection
    {
        return collect(self::actions)->flatMap(function ($actions, $model) {
            return collect($actions)->map(function ($action, $actionName) use ($model) {
                return "{$model}.{$actionName}";
            })->values();
        });
    }
}
