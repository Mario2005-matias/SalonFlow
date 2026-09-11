<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Room extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'description',
        'capacity',
        'location',
        'is_available'
    ];

    public function reserves()
    {
        return $this->hasMany(Reserve::class);
    }
}
