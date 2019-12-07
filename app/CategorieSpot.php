<?php

namespace App;

use App\Spot;
use Illuminate\Database\Eloquent\Model;

class CategorieSpot extends Model
{
    protected $table = 'categories_spots';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function spot()
    {
        return $this->hasMany(Spot::class);
    }
}
