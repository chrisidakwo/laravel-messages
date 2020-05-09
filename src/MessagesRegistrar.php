<?php

namespace ChrisIdakwo\Messages;

use ChrisIdakwo\Messages\Models\Message;
use ChrisIdakwo\Messages\Models\Room;
use ChrisIdakwo\Messages\Models\RoomMember;
use Illuminate\Database\Eloquent\Model;

class MessagesRegistrar {
    /**
     * Map for the messenger's models.
     *
     * @var array
     */
    protected static $models = [];

    /**
     * Map for the messenger's tables.
     *
     * @var array
     */
    protected static $tables = [];

    /**
     * Internal pointer name for the app's "user" model.
     *
     * @var string
     */
    private static $userModelLookupKey = 'user';

    /**
     * Set a full qualified class name of a model using the given key.
     *
     * @param   string $key
     * @param   string $model
     * @return  void
     */
    public static function setModelFQN($key, $model): void {
        static::$models[$key] = $model;
    }

    /**
     * Get the full qualified class name for the given key.
     *
     * @param   string $key
     * @return  string
     */
    public static function getModelFQN($key): string {
        return static::$models[$key];
    }

    /**
     * Set the fully qualified name for the user model.
     *
     * @param   string $model
     * @return  void
     */
    public static function setUserModelFQN($model): void {
        static::$models[self::$userModelLookupKey] = $model;
    }

    /**
     * Get the fully qualified name of the user model class.
     *
     * @return string
     */
    public static function getUserModelFQN(): string {
        return static::$models[self::$userModelLookupKey];
    }

    /**
     * Set custom table names.
     *
     * @param  array $map
     * @return void
     */
    public static function setTables(array $map): void {
        static::$tables = array_merge(static::$tables, $map);
    }

    /**
     * Get a custom table name mapping for the given table.
     *
     * @param  string $table
     * @return string
     */
    public static function getTable($table): string {
        return static::$tables[$table] ?? $table;
    }

    /**
     * Get an instance of the messages model.
     *
     * @param  array $attributes
     * @return Message
     */
    public static function message(array $attributes = []): Message {
        return static::make('message', $attributes);
    }

    /**
     * Get an instance of the room_members model.
     *
     * @param  array $attributes
     * @return RoomMember
     */
    public static function roomMember(array $attributes = []): RoomMember {
        return static::make('room_member', $attributes);
    }

    /**
     * Get an instance of the rooms model.
     *
     * @param  array $attributes
     * @return Room
     */
    public static function room(array $attributes = []): Room {
        return static::make('room', $attributes);
    }

    /**
     * Get an instance of the users model.
     *
     * @param   array $attributes
     * @return  Model
     */
    public static function user() {
        $model = static::getUserModelFQN();

        return new $model;
    }

    /**
     * Get an instance of the given model.
     *
     * @param  string $key
     * @param  array $attributes
     * @return Model|Message|Room|RoomMember
     */
    protected static function make($key, array $attributes = []) {
        $model = static::getModelFQN($key);

        return new $model($attributes);
    }
}
