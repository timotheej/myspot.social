<?php

namespace App;

use App\Spot;
use App\User;
use Illuminate\Database\Eloquent\Model;

class ImageSpot extends Model
{
    protected $table = 'images_spots';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['file_name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function spot()
    {
        return $this->belongsTo(Spot::class);
    }
}
