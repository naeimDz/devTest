<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'user_id',
        'status',
    ];
    protected $casts = [
        'user_id' => 'integer',
        'status' => 'string',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function requestService()
    {
        return $this->hasMany(RequestService::class);
    }
}
