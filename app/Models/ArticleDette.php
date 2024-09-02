<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ArticleDette extends Pivot
{
    // The table associated with the model
    protected $table = 'dette_article';

    // The attributes that are mass assignable
    protected $fillable = [
        'dette_id',
        'article_id',
        'quantite',
        'prix_unitaire',
    ];


    /**
     * Get the dette associated with the ArticleDette.
     */
    public function dette()
    {
        return $this->belongsTo(Dette::class);
    }

    /**
     * Get the article associated with the ArticleDette.
     */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
