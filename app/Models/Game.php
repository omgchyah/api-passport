<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dice1',
        'dice2',
        'result'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAverageSuccessPercentage(int $id)
    {
        //Count all games from user
        $allGames = Game::where('user_id', $id)->count();
        //Avoid division by zero
        if($allGames === 0) {
            return 0;
        }
        //Get total of wins
        $allWins = Game::where('user_id', $id)->where('result', 'w')->count();
        //Calculate success percentage
        return ($allWins * 100) / $allGames;
    }
}
