<?php

namespace ChrisIdakwo\Messages\Traits;

use Illuminate\Database\Schema\Blueprint;

trait ResolvesUUID {
    public function setPrimary(Blueprint $table, string $column, bool $nullable = false) {
        return (config('messages.model_keys.uses_uuid')) ? 
                    $table->uuid($column)->primary() : 
                    $table->bigIncrements($column);
    }

    public function setForeign(Blueprint $table, string $column, bool $nullable = false) {
        return (config('messages.model_keys.uses_uuid')) ? 
                    $table->uuid($column)->index()->nullable($nullable) :
                    $table->unsignedBigInteger($column)->index()->nullable($nullable);
    }
}
