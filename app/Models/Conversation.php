<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $table = 'conversations';

    protected $fillable = [
        'user_sender_id',
        'admin_receiver_id',
        'status',
    ];

    const WAITING = 0;
    const IN_PROGRESS = 1;
    const DONE = 2;

    public static $status = [
        self::WAITING => 'Đang chờ',
        self::IN_PROGRESS => 'Đang xử lý',
        self::DONE => 'Đã xong',
    ];

    public function conversation_messages()
    {
        return $this->hasMany('App\Models\ConversationMessage');
    }

    public function sender()
    {
        return $this->belongsTo('App\Models\User', 'user_sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo('App\Models\User', 'admin_receiver_id');
    }
}
