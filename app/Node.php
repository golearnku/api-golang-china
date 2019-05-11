<?php

namespace App;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Overtrue\LaravelFollow\Traits\CanBeSubscribed;

/**
 * Class Node.
 *
 * @author overtrue <i@overtrue.me>
 * @property int                                                   $node_id
 * @property string                                                $title
 * @property string                                                $icon
 * @property string                                                $banner
 * @property string                                                $description
 * @property object                                                $settings
 * @property object                                                $cache
 * @property \App\Node                                             $node
 * @property \Illuminate\Database\Eloquent\Relations\BelongsToMany $threads
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Node[] $children
 * @property-read mixed $has_subscribed
 * @property-read \App\Node $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $subscribers
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Node filter($input = array(), $filter = null)
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Node leaf()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Node newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Node newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Node onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Node paginateFilter($perPage = null, $columns = array(), $pageName = 'page', $page = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Node query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Node root()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Node simplePaginateFilter($perPage = null, $columns = array(), $pageName = 'page', $page = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Node whereBanner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Node whereBeginsWith($column, $value, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Node whereCache($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Node whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Node whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Node whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Node whereEndsWith($column, $value, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Node whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Node whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Node whereLike($column, $value, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Node whereNodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Node whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Node whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Node whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Node withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Node withoutTrashed()
 * @mixin \Eloquent
 */
class Node extends Model
{
    use SoftDeletes, Filterable, CanBeSubscribed;

    protected $fillable = [
        'node_id', 'title', 'icon', 'banner', 'description', 'settings', 'cache',
        'cache->threads_count', 'cache->subscribers_count',
    ];

    protected $casts = [
        'id' => 'int',
        'node_id' => 'int',
        'settings' => 'json',
        'cache' => 'json',
    ];

    protected $appends = [
        'has_subscribed',
    ];

    public function children()
    {
        return $this->hasMany(self::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('node_id');
    }

    public function scopeLeaf($query)
    {
        return $query->whereNotNull('node_id');
    }

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    public function refreshCache()
    {
        $this->update([
            'cache->threads_count' => $this->threads()->count(),
            'cache->subscribers_count' => $this->subscribers()->count(),
        ]);
    }

    public function getHasSubscribedAttribute()
    {
        return $this->isSubscribedBy(auth()->user());
    }
}
