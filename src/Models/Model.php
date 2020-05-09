<?php

namespace ChrisIdakwo\Messages\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Ramsey\Uuid\Uuid;

class Model extends EloquentModel {

    /**
     * Model constructor
     * 
     * @param array $attributes
     */
    public function __construct($attributes = []) {
        parent::__construct($attributes);

        $this->uuidSetup();
    }

    /**
     * Boot model
     *
     * @return void
     */
    public static function boot(): void {
        parent::boot();

        if (config('messages.model_keys.uses_uuid')) {
            static::creating(function ($model) {
                $model->id = Uuid::uuid4();
            });
        }
    }

    /**
     * Setup model if it uses a UUID as primary key
     *
     * @return void
     */
    private function uuidSetup() {
        if (config('messages.model_keys.uses_uuid')) {
            $this->setKeyType('string');
            $this->setIncrementing(false);
        }
    }
}
