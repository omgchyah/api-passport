<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'role'
    ];

    public function games()
    {
        return $this->hasMany(Game::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (is_null($model->username)) {
                $model->username = 'anonymous';
            }

            //Unique username bsides anonymous
            if ($model->username !== 'anonymous') {
                $existingUser = static::where('username', $model->username)->first();
                if ($existingUser && $existingUser->id !== $model->id) {
                    throw new \Exception("The username '" . $model->username . "' is already taken.");
                }
            }
        });

    }
}
