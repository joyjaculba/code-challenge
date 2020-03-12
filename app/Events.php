<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Validation\Validator;

class Events extends Model
{
	protected $table = "events";
    protected $fillable = ['event_name','from','to','weeks'];
}
