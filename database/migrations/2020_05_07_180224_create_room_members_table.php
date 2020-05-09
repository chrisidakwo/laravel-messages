<?php

use ChrisIdakwo\Messages\MessagesRegistrar;
use ChrisIdakwo\Messages\Traits\ResolvesUUID;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomMembersTable extends Migration {
    use ResolvesUUID;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create(MessagesRegistrar::getTable('room_members'), function (Blueprint $table) {
            $this->setPrimary($table, 'id');
            $this->setForeign($table, 'room_id');
            $this->setForeign($table, 'member_id');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists(MessagesRegistrar::getTable('room_members'));
    }
}
