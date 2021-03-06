<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Naux\Mail\SendCloudTemplate;
use Mail;

use App\Question;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'confirmation_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function owns(Question $model)
    {
        return $this->id == $model->user_id;
    }

    public function follows()
    {
        return  $this->belongsToMany(Question::class, 'user_question')->withTimestamps();
    }

    public function followThis($question)
    {
        return $this->follows()->toggle($question);
        //toggle 存在删除，不存在添加
    }

    public function followed($question)
    {
        return !!$this->follows()->where('question_id', $question)->count();//强制返回bool
        
    }
 
    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $data       = [
            'url'   => url('password/reset', $token),
        ];
        $template   = new SendCloudTemplate('zhihu_app_password_reset', $data);

        Mail::raw($template, function ($message) {
            $message->from('leiyisheng81@163.com', 'rayle');

            $message->to($this->email);
        });
    }
}
