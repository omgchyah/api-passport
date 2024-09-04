<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use App\Models\Game;

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
    
            // Unique username besides anonymous
            if ($model->username !== 'anonymous') {
                $existingUser = static::where('username', $model->username)->where('id', '!=', $model->id)->first();
                if ($existingUser) {
                    throw new \Exception("The username '" . $model->username . "' is already taken.");
                }
            }
        });
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Summary of calculateSuccessPercentage
     * @return float
     */
    public function calculateSuccessPercentage(): float
    {
        $totalGames = $this->games->count();
        $totalWins = $this->games->where('result', 'w')->count();

        return $totalGames > 0 ? ($totalWins * 100) / $totalGames : 0.0;
    }
    /**
     * Summary of calculateTotalSuccessPercentage
     * @return float|int
     */
    public static function calculateTotalSuccessPercentage(): float
    {
        $players = User::with('games')->whereIn('role', ['user', 'guest'])->get();
    
        $totalWins = 0;
        $totalGames = 0;
    
        foreach ($players as $player) {
            $totalGames += $player->games->count();
            $totalWins += $player->games->where('result', 'w')->count();
        }
    
        // Avoid division by zero if there are no games    
        return $totalGames === 0 ? 0.0 : ($totalWins * 100) / $totalGames;
    }
    /**
     * Summary of getWinner
     * @return User[]
     */
    public static function getWinner()
    {
        $players = User::with('games')->whereIn('role', ['user', 'guest'])->get();

        $winners = [];
        $highestSuccessPercentage = -1;

        foreach($players as $player) {
            $successPercentage = $player->calculateSuccessPercentage();
            if($successPercentage > $highestSuccessPercentage) {
                $highestSuccessPercentage = $successPercentage;
                $winners = [$player];
            } elseif($successPercentage === $highestSuccessPercentage) {
                $winners[] = $player;
            }
        }

        return $winners;
    }
    /**
     * Summary of getLoser
     * @return User[]
     */
    public static function getLoser()
    {
        $players = User::with('games')->whereIn('role', ['user', 'guest'])->get();

        $losers = [];
        $lowestSuccessPercentage = 101;

        foreach($players as $player) {
            $successPercentage = $player->calculateSuccessPercentage();

            if($successPercentage < $lowestSuccessPercentage) {
                $lowestSuccessPercentage = $successPercentage;
                $losers = [$player];
            } elseif($successPercentage === $lowestSuccessPercentage) {
                $losers[] = $player;
            }
        }

        return $losers;
    }

}
