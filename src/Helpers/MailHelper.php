<?php

namespace budisteikul\mail\Helpers;
use Illuminate\Support\Facades\Auth;
use budisteikul\mail\Models\Mail_Option;
use budisteikul\mail\Models\Mail_Email;

   class MailHelper {
   	public static function destroy_attachment($file,$user_id="")
   	{
   		if($user_id=="") $user_id = Auth::user()->id;
   		\Cloudinary::config(array( 
						"cloud_name" => env('CLOUDINARY_NAME'), 
						"api_key" => env('CLOUDINARY_KEY'), 
						"api_secret" => env('CLOUDINARY_SECRET') 
					));
					\Cloudinary\Uploader::destroy($file , Array('resource_type' => 'raw'));
   	}

   	public static function upload_attachment($file,$user_id="")
   	{
   		if($user_id=="") $user_id = Auth::user()->id;
   		\Cloudinary::config(array( 
							"cloud_name" => env('CLOUDINARY_NAME'), 
							"api_key" => env('CLOUDINARY_KEY'), 
							"api_secret" => env('CLOUDINARY_SECRET') 
						));
   		$upload = \Cloudinary\Uploader::upload($file , Array('resource_type' => 'raw','folder' => $user_id.'/attachments'));
			return $upload;
   	}

   	public static function get_email_from($string)
   	{
   	   		$pattern = '/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i';
    		preg_match_all($pattern, $string, $matches);
    		return $matches[0][0];
   	}

	   public static function get_string_between($string, $start, $end)
		{
			$string = ' ' . $string;
    		$ini = strpos($string, $start);
    		if ($ini == 0) return '';
    		$ini += strlen($start);
    		$len = strpos($string, $end, $ini) - $ini;
    		return substr($string, $ini, $len);
		}
		
	   public static function set_option($name,$value)
		{
			$user_id = Auth::user()->id;
			$result = Mail_Option::where('name',$name)->where('user_id',$user_id)->first();
			
			if(empty($result))
			{
				$result = new Mail_Option;
				$result->name = $name;
				$result->user_id = $user_id;
				$result->save();
			}
			
			$result = Mail_Option::where('user_id',$user_id)->where('name',$name)->first();
			$result->value = $value;
			$result->save();
			
		}
	
	public static function get_option($name, $user_id = "")
		{
			if($user_id=="") $user_id = Auth::user()->id;
			$result = Mail_Option::where('name',$name)->where('user_id',$user_id)->first();
			
			if(empty($result))
			{
				$result = new Mail_Option;
				$result->name = $name;
				$result->user_id = $user_id;
				$result->save();
			}
			
			$result = Mail_Option::where('user_id',$user_id)->where('name',$name)->first();
    		return $result->value;
		}
	   
	   public static function get_unread($folder)
		{
			return $mail = Mail_Email::where('user_id', Auth::user()->id)->where('read',0)->where('folder',$folder)->count();
		}
		
		public static function get_allmail($folder)
		{
			return $mail = Mail_Email::where('user_id', Auth::user()->id)->where('folder',$folder)->count();
		}

	   public static function mail_header($string)
	   {
			$header_arr = array();
			$test = json_decode($string);
			for($i=0;$i<count($test);$i++)
			{
				$header_arr[$test[$i][0]] = $test[$i][1];
			}
			return $header_arr;   
	   }
	   
	   public static function bytesToHuman($bytes)
	   {
       		$units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];

    		for ($i = 0; $bytes > 1024; $i++) {
        		$bytes /= 1024;
    		}

    	return round($bytes, 2) . ' ' . $units[$i];
		}
   }
   
?>