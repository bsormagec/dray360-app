<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\BelongsTo;

class Company extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Company::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'User Management';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name', 'email_intake_address', 'email_intake_address_alt',
    ];

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    public static $with = ['domain'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make('Id', 'id')->sortable(),
            Text::make('Name'),
            Text::make('Intake Email', 'email_intake_address'),
            Text::make('Intake Email Alt', 'email_intake_address_alt'),
            BelongsTo::make('Default TMS Provider', 'defaultTmsProvider', TmsProvider::class)->sortable(),
            Number::make('Automatic address verification threshold', 'automatic_address_verification_threshold'),
            Code::make('Ref Mapping', 'refs_custom_mapping')->json(),
            Code::make('Configuration', 'configuration')->json(),
            Boolean::make('Sync Addresses', 'sync_addresses'),
            BelongsTo::make('Domain', 'domain', Domain::class)->sortable()->nullable(),

            Text::make('Blackfly token', 'blackfly_token')->hideFromIndex(),
            Text::make('Blackfly imagetype', 'blackfly_imagetype')->hideFromIndex(),
            Text::make('Ripcms username', 'ripcms_username')->hideFromIndex(),
            Text::make('Ripcms password', 'ripcms_password')->hideFromIndex(),

            Text::make('Compcare organization id', 'compcare_organization_id')->hideFromIndex(),
            Text::make('Compcare username', 'compcare_username')->hideFromIndex(),
            Text::make('Compcare password', 'compcare_password')->hideFromIndex(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
