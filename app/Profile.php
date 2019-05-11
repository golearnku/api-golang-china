<?php

namespace App;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Profile.
 *
 * @author overtrue <i@overtrue.me>
 * @property \App\User      $user
 * @property int            $user_id
 * @property string         $from
 * @property string         $uid
 * @property string         $username
 * @property string         $name
 * @property string         $email
 * @property string         $location
 * @property string         $description
 * @property string         $avatar
 * @property string         $access_token
 * @property \Carbon\Carbon $access_token_expired_at
 * @property string         $access_token_secret
 * @property int $id
 * @property string|null $raw
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile filter($input = array(), $filter = null)
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Profile onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile paginateFilter($perPage = null, $columns = array(), $pageName = 'page', $page = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile simplePaginateFilter($perPage = null, $columns = array(), $pageName = 'page', $page = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereAccessTokenExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereAccessTokenSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereBeginsWith($column, $value, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereEndsWith($column, $value, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereLike($column, $value, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereRaw($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Profile withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Profile withoutTrashed()
 * @mixin \Eloquent
 */
class Profile extends Model
{
    use SoftDeletes, Filterable;

    protected $fillable = [
        'user_id', 'from', 'uid', 'username', 'name', 'email',
        'location', 'description', 'avatar',
        'access_token', 'access_token_expired_at', 'access_token_secret',
    ];

    protected $dates = [
        'access_token_expired_at',
    ];

    protected $casts = [
        'id' => 'int',
        'user_id' => 'int',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param \Overtrue\Socialite\User $socialiteUser
     * @param string                   $platform
     *
     * @return mixed
     */
    public static function createFromSocialite(\Overtrue\Socialite\User $socialiteUser, string $platform)
    {
        \Log::debug('socialite user.', $socialiteUser->toArray());

        $profile = self::updateOrCreate(['from' => $platform, 'uid' => $socialiteUser->getId()], [
            'username' => $socialiteUser->getUsername(),
            'name' => $socialiteUser->getName(),
            'email' => $socialiteUser->getEmail(),
            'location' => $socialiteUser->getOriginal()['location'] ?? null,
            'description' => $socialiteUser->getOriginal()['bio'] ?? '',
            'avatar' => $socialiteUser->getAvatar(),
            'access_token' => $socialiteUser->getAccessToken()->getToken(),
            'access_token_expired_at' => $socialiteUser->getAccessToken()->expired_at,
            'raw' => $socialiteUser->getOriginal(),
        ]);

        return $profile;
    }

    /**
     * @param Profile $profile
     *
     * @return mixed
     */
    protected static function getUserFromProfile(self $profile)
    {
        $user = User::whereEmail($profile->email)->first();
        $attributes = [
            'name' => $profile->name ?? $profile->email,
            'username' => $profile->username,
            'email' => $profile->email,
            'avatar' => $profile->avatar,
            'realname' => $profile->realname,
            'password' => null,
            'bio' => $profile->description,
            'extends' => [
                "{$profile->from}_id" => $profile->username,
                'location' => $profile->location,
                'company' => $profile->raw['company'] ?? '',
                'blog' => $profile->raw['blog'] ?? '',
            ],
        ];
        if ($user) {
            foreach (['realname', 'avatar', 'extends'] as $key) {
                if (empty($user->$key)) {
                    $user->$key = $attributes[$key];
                }
            }
        } else {
            $user = new User($attributes);
        }

        $user->activated_at = now();
        $user->save();

        return $user;
    }
}
