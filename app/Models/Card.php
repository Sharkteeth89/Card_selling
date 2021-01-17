<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    public function user(){
        return $this->belongsToMany(User::class, "user_cards")->withPivot('quantity','total_price')->orderBy('total_price', 'asc');
    }

    public function collection(){
        return $this->belongsToMany(Collection::class, "card_collections");
    }

    public function admin(){
        return $this->belongsTo(User::class);
    }
}
