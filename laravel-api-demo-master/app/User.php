<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
	// public $timestamps = false;

	use Notifiable;

	protected $fillable = [ 'name', 'email', 'password', ];

	protected $hidden = [ 'password', 'remember_token', ];

	protected $casts = [ 'email_verified_at' => 'datetime', ];
}
