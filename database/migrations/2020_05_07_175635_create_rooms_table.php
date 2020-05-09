<?php

use ChrisIdakwo\Messages\MessagesRegistrar;
use ChrisIdakwo\Messages\Traits\ResolvesUUID;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration {
    use ResolvesUUID;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create(MessagesRegistrar::getTable('rooms'), function (Blueprint $table) {
            $this->setPrimary($table, 'id');
            $this->setForeign($table, 'owner_id', true);
            $this->setForeign($table, 'context_id', true);
            
            $table->string('context_type')->index()->nullable();
            // $table->string('name')->index();
            $table->longText('topic')->index();
            $table->boolean('is_system_generated')->default(false)->comment('This column allows us to differentiate a user-created room from a system-generated room');
            
            $table->softDeletes();
            $table->timestamps();
        });

        // Context should be returned like this
        // return [
        //     "context" => [
        //         "monitor" => [
        //             "id" => "monitor id",
        //             "full_name" => "monitor name",
        //             "type" => "individual",
        //             "tagline" => "monitor tagline"
        //         ],

        //         "quotation" => [
        //             "id" => "quotation id",
        //             "price" => "$350",
        //             "negotiable" => true,
        //             "current_status" => "active",
        //         ],

        //         "task" => [
        //             "id" => "task id",
        //             "description" => "Lorem ipsum",
        //         ],

        //         "task_owner" => [
        //             "id" => "owner_id",
        //             "full_name" => "Daniel Walmart",
        //             "type" => "organization",
        //         ]
        //     ]
        // ];
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists(MessagesRegistrar::getTable('rooms'));
    }
}
