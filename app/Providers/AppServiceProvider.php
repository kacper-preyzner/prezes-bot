<?php

declare(strict_types=1);

namespace App\Providers;

use App\Data\ActionCollector;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(ActionCollector::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Date::use(\Carbon\CarbonImmutable::class);
        Model::shouldBeStrict();
        Model::unguard();
        Model::automaticallyEagerLoadRelationships();
    }
}
