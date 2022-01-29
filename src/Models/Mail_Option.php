<?php

namespace budisteikul\mail\Models;

use Illuminate\Database\Eloquent\Model;
use budisteikul\mail\Traits\Uuid;

class Mail_Option extends Model
{
    use Uuid;
	
	public $connection = $request->session()->get('session_connection', env('DB_CONNECTION');
	protected $table = 'mail_options';
	public $incrementing = false;
	protected $keyType = 'string';
	
	public function users()
    {
        return $this->belongsTo('budisteikul\coresdk\Models\User');
    }
}
