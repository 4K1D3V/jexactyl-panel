<?php

namespace Pterodactyl\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model as IlluminateModel;

/**
 * \Pterodactyl\Models\ActivityLog.
 *
 * @property int $id
 * @property string|null $batch
 * @property string $event
 * @property string|null $description
 * @property string|null $actor_type
 * @property int|null $actor_id
 * @property string|null $subject_type
 * @property int|null $subject_id
 * @property \Illuminate\Support\Collection $properties
 * @property string $timestamp
 * @property \Illuminate\Database\Eloquent\Model|\Eloquent $actor
 * @property \Illuminate\Database\Eloquent\Model|\Eloquent $subject
 *
 * @method static Builder|ActivityLog forAction(string $action)
 * @method static Builder|ActivityLog forActor(\Illuminate\Database\Eloquent\Model $actor)
 * @method static Builder|ActivityLog forSubject(\Illuminate\Database\Eloquent\Model $subject)
 * @method static Builder|ActivityLog newModelQuery()
 * @method static Builder|ActivityLog newQuery()
 * @method static Builder|ActivityLog query()
 * @method static Builder|ActivityLog whereAction($value)
 * @method static Builder|ActivityLog whereActorId($value)
 * @method static Builder|ActivityLog whereActorType($value)
 * @method static Builder|ActivityLog whereBatch($value)
 * @method static Builder|ActivityLog whereDescription($value)
 * @method static Builder|ActivityLog whereId($value)
 * @method static Builder|ActivityLog whereIp($value)
 * @method static Builder|ActivityLog whereProperties($value)
 * @method static Builder|ActivityLog whereSubjectId($value)
 * @method static Builder|ActivityLog whereSubjectType($value)
 * @method static Builder|ActivityLog whereTimestamp($value)
 * @mixin \Eloquent
 */
class ActivityLog extends Model
{
    public $timestamps = false;

    protected $guarded = [
        'id',
        'timestamp',
    ];

    protected $casts = [
        'properties' => 'collection',
    ];

    public static $validationRules = [
        'event' => ['required', 'string'],
        'batch' => ['nullable', 'uuid'],
        'description' => ['nullable', 'string'],
        'properties' => ['nullable', 'array'],
    ];

    public function actor(): MorphTo
    {
        return $this->morphTo();
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForAction(Builder $builder, string $action): Builder
    {
        return $builder->where('action', $action);
    }

    /**
     * Scopes a query to only return results where the actor is a given model.
     */
    public function scopeForActor(Builder $builder, IlluminateModel $actor): Builder
    {
        return $builder->whereMorphedTo('actor', $actor);
    }

    /**
     * Scopes a query to only return results where the subject is the given model.
     */
    public function scopeForSubject(Builder $builder, IlluminateModel $subject): Builder
    {
        return $builder->whereMorphedTo('subject', $subject);
    }
}
