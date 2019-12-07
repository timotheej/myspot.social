<?php

namespace App;

use App\Spot;
use App\User;
use Illuminate\Database\Eloquent\Model;

class RatingSpot extends Model
{
    protected $table = 'ratings_spots';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['user_id', 'spot_id', 'rating'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function spot()
    {
        return $this->hasMany(Spot::class);
    }
}
