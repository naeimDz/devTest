<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\RequestService;

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
    public function requestServices()
    {
        return $this->hasMany(RequestService::class);
    }
}
