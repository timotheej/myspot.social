<?php

namespace App;

use App\User;
use App\ImageSpot;
use App\CategorieSpot;
use Illuminate\Database\Eloquent\Model;

class Spot extends Model
{
    protected $table = 'spots';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['spot_id', 'lat', 'long'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the image spot providers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function imageSpot()
    {
        return $this->hasMany(ImageSpot::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categorie()
    {
        return $this->belongsTo(CategorieSpot::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function rating()
    {
        return $this->belongsToMany(RatingSpot::class);
    }
}
