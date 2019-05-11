<?php

namespace App;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Report
 *
 * @property int $id
 * @property int $user_id
 * @property int $reportable_id
 * @property string $reportable_type
 * @property string $remark
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report filter($input = array(), $filter = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report paginateFilter($perPage = null, $columns = array(), $pageName = 'page', $page = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report simplePaginateFilter($perPage = null, $columns = array(), $pageName = 'page', $page = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereBeginsWith($column, $value, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereEndsWith($column, $value, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereLike($column, $value, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereReportableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereReportableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereUserId($value)
 * @mixin \Eloquent
 */
class Report extends Model
{
    use Filterable;

    protected $fillable = [
        'user_id', 'remark',
    ];
}
