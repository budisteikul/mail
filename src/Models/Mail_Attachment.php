<?php

namespace budisteikul\mail\Models;

use Illuminate\Database\Eloquent\Model;
use budisteikul\mail\Traits\Uuid;
use Illuminate\Support\Facades\Session;

class Mail_Attachment extends Model
{
    use Uuid;
	
	public $connection = 'pgsql';
	protected $table = 'mail_attachments';
	public $incrementing = false;
	protected $keyType = 'string';
	
	public function mail_emails()
    {
        return $this->belongsTo('budisteikul\mail\Models\Mail_Email','email_id');
    }
}
