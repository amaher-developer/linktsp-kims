<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Ordering\Models\Cart;
use Modules\Ordering\Models\Order;
use Modules\Ordering\Policies\CartPolicy;
use Modules\Ordering\Policies\OrderPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Cart::class, CartPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);

        // Models live under Modules\<Name>\Models instead of App\Models, so
        // the default HasFactory resolver (which only knows App\Models ->
        // Database\Factories) can't find them. Map the module convention
        // instead: Modules\<Name>\Models\X -> Modules\<Name>\Database\Factories\XFactory.
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            if (preg_match('/^Modules\\\\(\w+)\\\\Models\\\\(.+)$/', $modelName, $matches)) {
                return "Modules\\{$matches[1]}\\Database\\Factories\\{$matches[2]}Factory";
            }

            return 'Database\\Factories\\'.class_basename($modelName).'Factory';
        });
    }
}
