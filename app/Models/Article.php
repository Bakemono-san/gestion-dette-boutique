<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['libelle', 'prix', 'quantite', 'user_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function dettes()
    {
        return $this->belongsToMany(Dette::class, 'dette_article')
                    ->using(ArticleDette::class)
                    ->withPivot('quantite', 'prix_unitaire')
                    ->withTimestamps();
    }
}
