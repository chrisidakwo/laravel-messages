<?php

namespace ChrisIdakwo\Messages\Tests;

use ChrisIdakwo\Messages\MessagesRegistrar;
use ChrisIdakwo\Messages\MessagesServiceProvider;
use ChrisIdakwo\Messages\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra {
    /**
     * Setup before each test
     *
     * @return void
     */
    public function setUp(): void {
        parent::setUp();

        Model::unguard();
        
        // Additional setup
        $this->migrateTables();
        $this->withFactories(__DIR__ . '/factories');

        // Seed User table
        $this->seedUsersTable();
    }

    /**
     * Get all package dependent service providers
     *
     * @param $app
     * @return array
     */
    protected function getPackageProviders($app) {
        return [
            MessagesServiceProvider::class,
        ];
    }

    /**
     * Setup package testing environment
     */
    protected function getEnvironmentSetUp($app) {
        // Set Messages User Model
        $app['config']->set('messages.models.user', 'ChrisIdakwo\Messages\Models\User');

        // Set all Messages Models
        $app['config']->set('messages.models.message', 'ChrisIdakwo\Messages\Models\Message');
        $app['config']->set('messages.models.room_member', 'ChrisIdakwo\Messages\Models\RoomMember');
        $app['config']->set('messages.models.room', 'ChrisIdakwo\Messages\Models\Room');

        // Set Laravel Auth User Model
        $app['config']->set('auth.providers.users.model', 'ChrisIdakwo\Messages\Models\User');

        // Set all database tables for Messages Models
        $app['config']->set('messages.table_names.messages', 'messages');
        $app['config']->set('messages.table_names.room_members', 'room_members');
        $app['config']->set('messages.table_names.rooms', 'rooms');

        $this->setUserModel();

        $this->setModels($app['config']);
    }

    /**
     * Run the migrations for the database.
     */
    private function migrateTables() {
        $this->createUsersTable();
        $this->createRoomsTable();
        $this->createRoomMembersTable();
        $this->createMessagesTable();
    }

     /**
     * Create the users table in the database.
     */
    private function createUsersTable() {
        if (!Schema::hasTable('users')) {
           Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamps();
            });
        }
    }

    /**
     * Create some users for the tests to use.
     */
    private function seedUsersTable() {
        factory(User::class, 3)->create();
    }

    /**
     * Create the rooms table in the database.
     */
    private function createRoomsTable() {
        include_once __DIR__ . '/../database/migrations/2020_05_07_175635_create_rooms_table.php';

        (new \CreateRoomsTable)->up();
    }

    /**
     * Create the rooms table in the database.
     */
    private function createRoomMembersTable() {
        include_once __DIR__ . '/../database/migrations/2020_05_07_180224_create_room_members_table.php';

        (new \CreateRoomMembersTable)->up();
    }

    /**
     * Create the messages table in the database.
     */
    private function createMessagesTable() {
        include_once __DIR__ . '/../database/migrations/2020_05_07_175710_create_messages_table.php';

        (new \CreateMessagesTable)->up();
    }

    /**
     * Define User model in Messages's model registry.
     *
     * @return void
     */
    protected function setUserModel() {
        MessagesRegistrar::setUserModelFQN(User::class);

        MessagesRegistrar::setTables([
            'users' => (new User)->getTable(),
        ]);
    }

    /**
     * Define messages's models in registry.
     *
     * @return void
     */
    protected function setModels($config) {
        MessagesRegistrar::setModelFQN('message', $config->get('messages.models.message'));
        MessagesRegistrar::setModelFQN('room', $config->get('messages.models.room'));
        MessagesRegistrar::setModelFQN('room_member', $config->get('messages.models.room_member'));

        MessagesRegistrar::setTables([
            'messages' => $config->get('messages.table_names.messages'),
            'room_members' => $config->get('messages.table_names.room_members'),
            'rooms' => $config->get('messages.table_names.rooms'),
        ]);
    }
}