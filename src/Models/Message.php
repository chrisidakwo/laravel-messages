<?php

namespace ChrisIdakwo\Messages\Models;

use ChrisIdakwo\Messages\MessagesRegistrar;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'messages';

    /**
     * The relationships that should be touched on save.
     *
     * @var array
     */
    protected $touches = ['room'];

    /**
     * The attributes that can be set with Mass Assignment.
     *
     * @var array
     */
    protected $fillable = ['room_id', 'sender_id', 'body'];

    /**
     * {@inheritDoc}
     */
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);

        $this->setTable(MessagesRegistrar::getTable('messages'));
    }

    /**
     * Room relationship.
     *
     * @return BelongsTo
     *
     * @codeCoverageIgnore
     */
    public function room(): BelongsTo {
        return $this->belongsTo(MessagesRegistrar::getModelFQN('room'), 'room_id', 'id');
    }

    /**
     * Sender relationship. Directly relates to the users table.
     *
     * @return BelongsTo
     *
     * @codeCoverageIgnore
     */
    public function sender(): BelongsTo {
        return $this->belongsTo(MessagesRegistrar::getUserModelFQN(), 'sender_id');
    }

    /**
     * Room member relationship.
     *
     * @return HasMany
     *
     * @codeCoverageIgnore
     */
    public function roomMembers(): HasMany {
        return $this->hasMany(MessagesRegistrar::getModelFQN('room_member'), 'room_id', 'room_id');
    }

    /**
     * Recipients of this message.
     *
     * @return HasMany
     */
    public function recipients(): HasMany {
        return $this->roomMembers()->where('member_id', '!=', $this->sender_id);
    }

    // /**
    //  * Returns unread messages given the userId.
    //  *
    //  * @param \Illuminate\Database\Eloquent\Builder $query
    //  * @param int $userId
    //  * @return \Illuminate\Database\Eloquent\Builder
    //  */
    // public function scopeUnreadForUser(Builder $query, $userId) {
    //     return $query->has('thread')
    //         ->where('user_id', '!=', $userId)
    //         ->whereHas('participants', function (Builder $query) use ($userId) {
    //             $query->where('user_id', $userId)
    //                 ->where(function (Builder $q) {
    //                     $q->where('last_read', '<', $this->getConnection()->raw($this->getConnection()->getTablePrefix() . $this->getTable() . '.created_at'))
    //                         ->orWhereNull('last_read');
    //                 });
    //         });
    // }
}
