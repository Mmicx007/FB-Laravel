<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFacebookPage extends Model
{
    use HasFactory;

    protected $table = 'user_facebook_pages';

    protected $fillable = [
        'user_id',
        'page_id',
        'name',
        'cover_url',
        'email',
        'username',
        'access_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'page_id');
    }
    

}
