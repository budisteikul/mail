<?php

namespace budisteikul\mail\Models;

use Illuminate\Database\Eloquent\Model;
use budisteikul\mail\Traits\Uuid;
use Illuminate\Support\Facades\Session;

class Mail_Email extends Model
{
    use Uuid;
	
	public $connection = 'pgsql';
	protected $table = 'mail_emails';
	public $incrementing = false;
	protected $keyType = 'string';
	
	public function users()
    {
        return $this->belongsTo('budisteikul\coresdk\Models\User');
    }
	
	public function mail_attachments()
	{
		return $this->hasMany('budisteikul\mail\Models\Mail_Attachment','email_id');
	}
}
