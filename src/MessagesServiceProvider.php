<?php

namespace ChrisIdakwo\Messages;

use Illuminate\Support\ServiceProvider;

class MessagesServiceProvider extends ServiceProvider {
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {
        $this->offerPublishing();
        $this->setUserModel();
        $this->setModels();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        $this->configure();
    }

    /**
     * Setup the configuration for Messages.
     *
     * @return void
     */
    protected function configure() {
        $this->mergeConfigFrom(__DIR__ . '/../config/messages.php', 'messages');
    }

    /**
     * Setup the resource publishing groups for Messages.
     *
     * @return void
     */
    protected function offerPublishing() {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/messages.php' => config_path('messages.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../database/migrations' => base_path('database/migrations'),
            ], 'migrations');
        }
    }

    /**
     * Define messages's models in registry.
     *
     * @return void
     */
    protected function setModels() {
        $config = $this->app->make('config');

        MessagesRegistrar::setModelFQN('message', $config->get('messages.models.message'));
        MessagesRegistrar::setModelFQN('room', $config->get('messages.models.room'));
        MessagesRegistrar::setModelFQN('room_member', $config->get('messages.models.room_member'));

        MessagesRegistrar::setTables([
            'messages' => $config->get('messages.table_names.messages'),
            'room_members' => $config->get('messages.table_names.room_members'),
            'rooms' => $config->get('messages.table_names.rooms'),
        ]);
    }

    /**
     * Define User model in Messages's model registry.
     *
     * @return void
     */
    protected function setUserModel() {
        $config = $this->app->make('config');

        $model = $config->get('messages.models.user', function () use ($config) {
            return $config->get('auth.providers.users.model', $config->get('auth.model'));
        });

        MessagesRegistrar::setUserModelFQN($model);

        MessagesRegistrar::setTables([
            'users' => (new $model)->getTable(),
        ]);
    }
}
