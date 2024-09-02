<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dette extends Model
{
    use HasFactory;
    protected $table = 'dettes';

    protected $fillable = [
        'client_id',
        'montant_total',
        'montant_paye',
        'etat',
    ];

    /**
     * Get the client associated with the debt.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * The articles associated with the debt.
     */

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_dettes')
            ->using(ArticleDette::class)
            ->withPivot('quantite', 'prix_unitaire')
            ->withTimestamps();
    }


    /**
     * Get the payments associated with the debt.
     */
    // public function paiements(): HasMany
    // {
    //     return $this->hasMany(Paiement::class);
    // }
}
