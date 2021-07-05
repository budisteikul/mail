<?php

namespace budisteikul\mail\Controllers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use budisteikul\mail\Helpers\MailHelper;
use budisteikul\mail\Models\Mail_Account;
use budisteikul\mail\Models\Mail_Email;
use budisteikul\mail\Models\Mail_Attachment;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class WebhookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $mail = Mail_Account::where('email',$request->input('recipient'))->first();
			if(!$mail) exit();
			$user_id = $mail->user_id;
			
			$folder = "inbox";
			if($request->input('message-headers') != "")
			{
				$MailHeader = MailHelper::mail_header($request->input('message-headers'));
				if (array_key_exists('X-Mailgun-Sflag', $MailHeader))
				{
					$XMailgunSflag = $MailHeader['X-Mailgun-Sflag'];
				}
				else
				{
					$XMailgunSflag = "No";
				}
			
				if($XMailgunSflag=="Yes")
				{
					$folder = "junk";
				}
			}
			
			
			$timestamp = $request->input('timestamp');
			if($request->input('timestamp')=="")  $timestamp = Carbon::now();
			
		    $mail_email = new Mail_Email;
			$mail_email->recipient = $request->input('recipient');
			$mail_email->sender = $request->input('sender');
			$mail_email->from = $request->input('from');
			$mail_email->subject = $request->input('subject');
			$mail_email->body_plain = $request->input('body-plain');
			$mail_email->stripped_text = $request->input('stripped-text');
			$mail_email->stripped_signature = $request->input('stripped-signature');
			$mail_email->body_html = $request->input('body-html');
			$mail_email->stripped_html = $request->input('stripped-html');
			$mail_email->attachment_count = $request->input('attachment-count');
			$mail_email->attachment_x = $request->input('attachment-x');
			$mail_email->timestamp = $timestamp;
			$mail_email->signature = $request->input('signature');
			$mail_email->message_headers = $request->input('message-headers');
			$mail_email->content_id_map = $request->input('content-id-map');
			$mail_email->folder = $folder;
			$mail_email->user_id = $user_id;
			$mail_email->save();
			
			
			if($request->input('attachment-count')>0)
			{
				for($i=1;$i<=$request->input('attachment-count');$i++)
				{
					//if(env('FILESYSTEM_DRIVER')=="cloudinary")
					//{
						\Cloudinary::config(array( 
							"cloud_name" => env('CLOUDINARY_NAME'), 
							"api_key" => env('CLOUDINARY_KEY'), 
							"api_secret" => env('CLOUDINARY_SECRET') 
						));
						$upload = \Cloudinary\Uploader::upload($request->file('attachment-'. $i) , Array('resource_type' => 'raw','folder' => $user_id.'/attachments'));
						$path = $upload['public_id'];
						$url = $upload['secure_url'];
					//}
					//else
					//{
						//$path = Storage::disk('public')->putFile('mails/attachments', $request->file('attachment-'. $i));
						//$url = Storage::url($path);
					//}
					
					$mail_attachment = new Mail_Attachment;
					$mail_attachment->email_id = $mail_email->id;
					$mail_attachment->file_path = $path;
					$mail_attachment->file_url = $url;
					$mail_attachment->file_name = $request->file('attachment-'. $i)->getClientOriginalName();
					$mail_attachment->file_mimetype = $request->file('attachment-'. $i)->getClientMimeType();
					$mail_attachment->file_size = $request->file('attachment-'. $i)->getSize();
					$mail_attachment->save();
				}
			}
			
			
			$pushover_user = MailHelper::get_option('pushover_user',$user_id);
			$pushover_app = MailHelper::get_option('pushover_app',$user_id);
			
			if($pushover_app!="" && $pushover_user!="" && $folder=="inbox" && $mail->notify==1)
			{
		
				$url_link = url('/mails/'. $mail_email->id .'/html');
				curl_setopt_array($ch = curl_init(), array(
  				CURLOPT_URL => "https://api.pushover.net/1/messages.json",
  				CURLOPT_POSTFIELDS => array(
    			"token" => $pushover_app,
    			"user" => $pushover_user,
				"title" => $request->input('from'),
    			"message" => $request->input('subject'),
				"url" => $url_link,
				"url_title" => "View message",
  				),
				));
				curl_exec($ch);
				curl_close($ch);
		
			}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
