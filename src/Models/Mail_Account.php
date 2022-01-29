<?php

namespace budisteikul\mail\Models;

use Illuminate\Database\Eloquent\Model;
use budisteikul\mail\Traits\Uuid;

class Mail_Account extends Model
{
    use Uuid;
	
	public $connection = "pgsql";
	protected $table = 'mail_accounts';
	public $incrementing = false;
	protected $keyType = 'string';
	
	public function users()
    {
        return $this->belongsTo('budisteikul\coresdk\Models\User');
    }
}
