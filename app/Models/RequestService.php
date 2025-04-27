<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Eloquent\Relations\HasMany;


class RequestService extends Model
{
    use HasFactory;
    protected $table = 'requestservices';
    

    protected $fillable = [
        'service_id',
        'user_id',
        'status',
        'email',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
