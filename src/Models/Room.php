<?php

namespace ChrisIdakwo\Messages\Models;

use ChrisIdakwo\Messages\MessagesRegistrar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Room extends Model {
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'rooms';

    /**
     * The attributes that can be set with Mass Assignment.
     *
     * @var array
     */
    protected $fillable = ['owner_id', 'context_id', 'context_type', 'topic', 'is_system_generated'];

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

        $this->setTable(MessagesRegistrar::getTable('rooms'));
    }

    /**
     * Returns the latest message from a thread.
     *
     * @return null|Model
     */
    public function getLatestMessageAttribute() {
        return $this->messages()->latest()->first();
    }

    /**
     * Find a room by the topic.
     *
     * @param string $topic
     *
     * @return Model[]|Collection
     */
    public static function findByTopic($topic) {
        return static::where('topic', 'LIKE', "%{$topic}%")->get();
    }

    /**
     * Returns an array of ids for users that are associated with the room.
     *
     * @param null|int $userId
     *
     * @return array
     */
    public function getMembersUserIds($userId = null): array {
        $users = $this->members()->withTrashed()->get()->pluck('member_id');

        if ($userId !== null) {
            $users->push($userId);
        }

        return $users->toArray();
    }

    /**
     * Returns rooms that the user is associated with.
     *
     * @param Builder $query
     * @param int $userId
     *
     * @return Builder
     */
    public function scopeForUser(Builder $query, $userId): Builder {
        $roomMembersTable = MessagesRegistrar::getTable('room_members');
        $roomsTable = $this->getTable();

        return $query->join($roomMembersTable, $this->getQualifiedKeyName(), '=', $roomMembersTable . '.room_id')
            ->where($roomMembersTable . '.member_id', $userId)
            ->whereNull($roomMembersTable . '.deleted_at')
            ->select($roomsTable . '.*');
    }

    /**
     * Returns rooms with new messages that the user is associated with.
     *
     * @param Builder $query
     * @param int $userId
     *
     * @return Builder
     */
    public function scopeForUserWithLatestMessages(Builder $query, $userId): Builder {
        $roomMembersTable = MessagesRegistrar::getTable('room_members');
        $messagesTable = MessagesRegistrar::getTable('messages');
        $roomsTable = $this->getTable();

        return $query->join($roomMembersTable, $this->getQualifiedKeyName(), '=', $roomMembersTable . '.room_id')
                ->where($roomMembersTable . 'member_id', $userId)
                ->whereNull($roomMembersTable . 'deleted_at')
                ->join($messagesTable, $messagesTable . 'room_id', $roomsTable . 'id')
                ->latest($messagesTable . 'created_at')
                ->select($roomsTable . '*');

        // return $query->join($roomMembersTable, $this->getQualifiedKeyName(), '=', $roomMembersTable . '.room_id')
        //     ->where($roomMembersTable . '.member_id', $userId)
        //     ->whereNull($roomMembersTable . '.deleted_at')
        //     ->where(function (Builder $query) use ($roomMembersTable, $roomsTable) {
        //         $query->where($roomsTable . '.updated_at', '>', $this->getConnection()->raw($this->getConnection()->getTablePrefix() . $roomMembersTable . '.last_read'))
        //             ->orWhereNull($roomMembersTable . '.last_read');
        //     })
        //     ->select($roomsTable . '.*');
    }

    /**
     * Returns rooms shared by the given user ids.
     *
     * @param Builder $query
     * @param array $roomMembers
     *
     * @return Builder
     */
    public function scopeBetween(Builder $query, array $roomMembers): Builder {
        return $query->whereHas('members', function (Builder $q) use ($roomMembers) {
            $q->whereIn('member_id', $roomMembers)
                ->select($this->getConnection()->raw('DISTINCT(room_id)'))
                ->groupBy('room_id')
                ->havingRaw('COUNT(room_id)' . '=' . count($roomMembers));
        });
    }

    /**
     * Add members to a room.
     *
     * @param array|mixed $userId
     *
     * @return \Illuminate\Support\Collection|Model[]
     */
    public function addMembers($userId) {
        $userIds = is_array($userId) ? $userId : (array) func_get_args();

        $roomMemberModel = MessagesRegistrar::roomMember();

        collect($userIds)->each(function ($userId) use ($roomMemberModel) {
            $this->members()->save(new $roomMemberModel([
                'member_id' => $userId,
                'room_id' => $this->id,
            ]));
        });

        return $this->members;
    }

    /**
     * Remove members from a room.
     *
     * @param array|mixed $userId
     *
     * @return void
     */
    public function removeMembers($userId): void {
        $userIds = is_array($userId) ? $userId : (array) func_get_args();

        $roomMemberModel = MessagesRegistrar::roomMember();

        $roomMemberModel::query()->where('room_id', $this->refresh()->id)->whereIn('member_id', $userIds)->delete();
    }

    /**
     * Finds the membership record from a user id.
     *
     * @param   int|string $userId
     * @return  mixed
     * @throws  ModelNotFoundException
     */
    public function getMembershipFromUser($userId) {
        return $this->members()->where('member_id', $userId)->firstOrFail();
    }

    /**
     * Generates an array of room members information.
     *
     * @return array
     */
    public function getMembersInformation(): array {
        return $this->users()->get()->pluck('full_name')->toArray();
    }

    /**
     * Checks to see if a user is a current member of the group.
     *
     * @param int|string $userId
     *
     * @return bool
     */
    public function hasMember($userId): bool {
        return $this->members()->where('member_id', $userId)->exists();
    }

    /**
     * Alias for the owner() method.
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo {
        return $this->owner();
    }

    /**
     * Messages relationship.
     *
     * @return HasMany
     *
     * @codeCoverageIgnore
     */
    public function messages(): HasMany {
        return $this->hasMany(MessagesRegistrar::getModelFQN('message'), 'room_id', 'id');
    }

    /**
     * Members relationship (directly referencing the room_members joint table).
     *
     * @return HasMany
     *
     * @codeCoverageIgnore
     */
    public function members(): HasMany {
        return $this->hasMany(MessagesRegistrar::getModelFQN('room_member'), 'room_id', 'id');
    }

    /**
     * Members relationship (directly referencing the user model of the associated members).
     *
     * @return BelongsToMany
     *
     * @codeCoverageIgnore
     */
    public function users(): BelongsToMany {
        return $this->belongsToMany(MessagesRegistrar::getUserModelFQN(), MessagesRegistrar::getTable('room_members'), 'room_id', 'member_id');
    }

    /**
     * Owner's relationship. References the user that owns the room.
     *
     * @return BelongsTo
     *
     * @codeCoverageIgnore
     */
    public function owner(): BelongsTo {
        return $this->belongsTo(MessagesRegistrar::getUserModelFQN(), 'owner_id');
    }
}
