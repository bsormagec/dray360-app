<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Blueprint;

class MacrosServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blueprint::macro('dropColumnIfExists', function ($columnName, $foreignKeyName = null) {
            $tableName = $this->getTable();
            if (! Schema::hasColumn($tableName, $columnName)) {
                return;
            }

            $foreignKeys = Schema::getConnection()
                ->getDoctrineSchemaManager()
                ->listTableForeignKeys($tableName);

            $foreignKeys = collect($foreignKeys)->map(fn ($key) => $key->getName());
            $foreignKeyName = $foreignKeyName ?? ($tableName.'_'.$columnName.'_foreign');

            if ($foreignKeys->contains($foreignKeyName)) {
                $this->dropForeign($foreignKeyName);
            }
            // now remove the column
            $this->dropColumn($columnName);
        });
    }
}
