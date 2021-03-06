<?php

    namespace ffy\mailbox;

    use App\User;
    use Carbon\Carbon;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;

    class Thread extends Model
    {
        protected $table = 'ffy_mailbox_thread';
        protected $fillable = ['title', 'author_id', 'recipient_id'];

        public function author()
        {
            return $this->belongsTo(User::class, 'author_id');
        }

        public function recipient()
        {
            return $this->belongsTo(User::class, 'recipient_id');
        }

        public function messages()
        {
            return $this->hasMany(Message::class)->orderBy('created_at', 'desc');
        }

        public function threadMessages($date)
        {
            return $this->messages()->where('created_at', '>=', $date)->get();
        }

        public function last_message()
        {
            return $this->hasOne(Message::class, 'thread_id')->latest();
        }

        public function recipients()
        {
            return $this->belongsToMany(User::class, 'ffy_mailbox_thread_recipient')->withPivot('created_at');
        }

        public function getLatestMessageAttribute()
        {
            return $this->last_message()->first();
        }

        public function hasRecipient($id)
        {
            return $this->recipients->contains($id);
        }

        public function otherRecipient($id)
        {
            return $this->author->id == $id ? $this->recipient : $this->author;
        }
    }
