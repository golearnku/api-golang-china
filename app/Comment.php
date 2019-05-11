<?php

namespace App;

use App\Contracts\Commentable;
use App\Traits\OnlyActivatedUserCanCreate;
use App\Traits\WithDiffForHumanTimes;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Overtrue\LaravelFollow\Traits\CanBeVoted;

/**
 * Class Comment.
 *
 * @author overtrue <i@overtrue.me>
 * @property int       $commentable_id
 * @property string    $commentable_type
 * @property int       $user_id
 * @property bool      $banned_at
 * @property object    $cache
 * @property \App\User $user
 * @property Model     commentable
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $commentable
 * @property-read \App\Content $content
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $downvoters
 * @property-read mixed $down_voters
 * @property-read mixed $has_down_voted
 * @property-read mixed $has_up_voted
 * @property-read mixed $up_voters
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $upvoters
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $voters
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment filter($input = array(), $filter = null)
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Comment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment paginateFilter($perPage = null, $columns = array(), $pageName = 'page', $page = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment simplePaginateFilter($perPage = null, $columns = array(), $pageName = 'page', $page = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment valid()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereBannedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereBeginsWith($column, $value, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereCache($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereCommentableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereCommentableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereEndsWith($column, $value, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereLike($column, $value, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Comment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Comment withoutTrashed()
 * @mixin \Eloquent
 */
class Comment extends Model
{
    use SoftDeletes, Filterable, CanBeVoted, OnlyActivatedUserCanCreate, WithDiffForHumanTimes;

    const COMMENTABLES = [
        Thread::class,
    ];

    protected $fillable = [
        'commentable_id', 'commentable_type', 'user_id', 'banned_at', 'cache',
    ];

    protected $dates = [
        'banned_at',
    ];

    protected $with = [
        'user', 'content',
    ];

    protected $casts = [
        'id' => 'int',
        'user_id' => 'int',
        'cache' => 'object',
    ];

    protected $appends = [
        'has_up_voted', 'has_down_voted', 'up_voters', 'down_voters', 'created_at_timeago', 'updated_at_timeago',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($comment) {
            $comment->user_id = \auth()->id();
        });

        $saveContent = function ($comment) {
            if (request()->routeIs('comments.*') && \request()->has('content')) {
                $data = array_only(\request()->input('content', []), \request()->input('type', 'markdown'));
                $comment->content()->updateOrCreate(['contentable_id' => $comment->id], $data);
                $comment->loadMissing('content');
            }
        };

        static::updated($saveContent);
        static::created($saveContent);

        static::saved(function (Comment $comment) {
            $comment->user->increment('energy', User::ENERGY_COMMENT_CREATE);
        });

        static::deleted(function (Comment $comment) {
            $comment->user->increment('energy', User::ENERGY_COMMENT_DELETE);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commentable()
    {
        return $this->morphTo();
    }

    public function content()
    {
        return $this->morphOne(Content::class, 'contentable');
    }

    public function scopeValid($query)
    {
        $query->whereHas('user', function ($q) {
            $q->whereNotNull('activated_at')->whereNull('banned_at');
        });
    }

    public static function isCommentable($target)
    {
        if (\is_object($target)) {
            return $target instanceof Commentable;
        }

        $ref = new \ReflectionClass($target);

        return $ref->isSubclassOf(Commentable::class);
    }

    public function getHasUpVotedAttribute()
    {
        return $this->isUpvotedBy(auth()->user());
    }

    public function getHasDownVotedAttribute()
    {
        return $this->isDownvotedBy(auth()->user());
    }

    public function getUpVotersAttribute()
    {
        return $this->upvoters()->count();
    }

    public function getDownVotersAttribute()
    {
        return $this->downvoters()->count();
    }
}
