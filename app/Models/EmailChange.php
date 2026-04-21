<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailChange extends Model
{
    protected $fillable = ['user_id','new_email','token'];

    public function user()
{
    return $this->belongsTo(User::class);
}

}
