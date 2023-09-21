<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $table = 'user_page_campaign';

    protected $fillable = [
        'page_id',
        'name',
        'budget',
        'target_audience',
        'ad_content',
        'start_date',
        'end_date',
    ];

    public function page()
    {
        return $this->belongsTo(UserFacebookPage::class, 'page_id');
    }
}
