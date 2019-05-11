<?php

namespace App;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Banner.
 *
 * @author overtrue <i@overtrue.me>
 * @property string $name
 * @property string $description
 * @property array  $banners
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Banner filter($input = array(), $filter = null)
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Banner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Banner newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Banner onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Banner paginateFilter($perPage = null, $columns = array(), $pageName = 'page', $page = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Banner query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Banner simplePaginateFilter($perPage = null, $columns = array(), $pageName = 'page', $page = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Banner whereBanners($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Banner whereBeginsWith($column, $value, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Banner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Banner whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Banner whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Banner whereEndsWith($column, $value, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Banner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Banner whereLike($column, $value, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Banner whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Banner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Banner withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Banner withoutTrashed()
 * @mixin \Eloquent
 */
class Banner extends Model
{
    use SoftDeletes, Filterable;

    protected $fillable = [
        'name', 'description', 'banners',
    ];

    protected $casts = [
        'banners' => 'array',
    ];

    public function getRouteKeyName()
    {
        return 'name';
    }
}
