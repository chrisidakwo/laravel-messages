<?php

namespace ChrisIdakwo\Messages\Models;

use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use ChrisIdakwo\Messages\MessagesRegistrar;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomMember extends Model {
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'room_members';

    /**
     * The attributes that can be set with Mass Assignment.
     *
     * @var array
     */
    protected $fillable = ['room_id', 'member_id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * {@inheritDoc}
     */
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);

        $this->table = MessagesRegistrar::getTable('room_members');
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
     * Member relationship. Directly relates to the users table
     *
     * @return BelongsTo
     *
     * @codeCoverageIgnore
     */
    public function member(): BelongsTo {
        return $this->belongsTo(MessagesRegistrar::getUserModelFQN(), 'member_id');
    }
}
