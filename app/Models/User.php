<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Mail\VerificationEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendVerificationEmailJob;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function generateOtp()
    {
        $this->otp = rand(100000, 999999);
        $this->save();
    }

    public function sendVerificationEmail()
    {
        dispatch(new SendVerificationEmailJob($this));
        // not work redis with me because my app dowsnt connect with the redis server by redis
        //use this if yo want send email as ablock
        // Mail::to($this->email)->send(new VerificationEmail($this));
    }

    public function verifyEmail($otp)
    {
        if ($this->otp == $otp) {
            $this->is_verified = true;
            $this->otp = null;
            $this->save();
            return true;
        }
        return false;
    }
}
