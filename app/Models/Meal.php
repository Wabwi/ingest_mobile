<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meal extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'uuid',
        'meal_type',
        'description',
        'eaten_at',
        'synced',
    ];

    protected $casts = [
        'eaten_at' => 'datetime',
        'synced' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($meal) {
            if (empty($meal->uuid)) {
                $meal->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
