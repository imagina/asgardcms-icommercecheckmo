<?php

namespace Modules\Icommercecheckmo\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Core\Events\BuildingSidebar;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\Icommercecheckmo\Events\Handlers\RegisterIcommercecheckmoSidebar;

class IcommercecheckmoServiceProvider extends ServiceProvider
{
    use CanPublishConfiguration;
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();
        $this->app['events']->listen(BuildingSidebar::class, RegisterIcommercecheckmoSidebar::class);

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('icommercecheckmos', array_dot(trans('icommercecheckmo::icommercecheckmos')));
            // append translations

        });
    }

    public function boot()
    {
        $this->publishConfig('icommercecheckmo', 'permissions');
        $this->publishConfig('icommercecheckmo', 'config');

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    private function registerBindings()
    {
        $this->app->bind(
            'Modules\Icommercecheckmo\Repositories\IcommerceCheckmoRepository',
            function () {
                $repository = new \Modules\Icommercecheckmo\Repositories\Eloquent\EloquentIcommerceCheckmoRepository(new \Modules\Icommercecheckmo\Entities\IcommerceCheckmo());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Icommercecheckmo\Repositories\Cache\CacheIcommerceCheckmoDecorator($repository);
            }
        );
// add bindings

    }
}
