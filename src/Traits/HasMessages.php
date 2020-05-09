<?php

namespace ChrisIdakwo\Messages\Traits;

use ChrisIdakwo\Messages\MessagesRegistrar;

trait HasMessages {

    /**
     * Returns true, if user is a member of the given room id. False otherwise.
     * 
     * @param   int|string $roomId
     * @return  bool
     */
    public function isARoomMember($roomId): bool {
        return $this->roomMembers()->where('member_id', $this->id)->Where('room_id', $roomId)->exists();
    }

    /**
     * Message relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @codeCoverageIgnore
     */
    public function messages() {
        return $this->hasMany(MessagesRegistrar::getModelFQN('message'), 'sender_id');
    }

    /**
     * Room members relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @codeCoverageIgnore
     */
    public function roomMembers() {
        return $this->hasMany(MessagesRegistrar::getModelFQN('room_member'), 'member_id');
    }

    /**
     * Room relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     *
     * @codeCoverageIgnore
     */
    public function rooms() {
        return $this->belongsToMany(
            MessagesRegistrar::getModelFQN('room'), MessagesRegistrar::getTable('room_members'), 'member_id', 'room_id'
        );
    }
}
