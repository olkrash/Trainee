<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResetPassword extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reset_password';

    /**
     * @var string[]
     */
    protected $fillable = ['user_id', 'token'];

    protected $dates = ['created_at', 'updated_at'];

    use HasFactory;
}
