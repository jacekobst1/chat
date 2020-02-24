<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    const UPDATED_AT = null;
    protected $fillable = [
      'user_id',
      'content'
    ];

    public function User() {
        return $this->belongsTo('/app/User');
    }
}
