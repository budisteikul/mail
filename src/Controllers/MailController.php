<?php

namespace budisteikul\mail\Controllers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use budisteikul\mail\DataTables\MailsDataTable;
use budisteikul\mail\Models\Mail_Account;
use budisteikul\mail\Models\Mail_Email;
use budisteikul\mail\Models\Mail_Attachment;
use budisteikul\mail\Helpers\MailHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use budisteikul\mail\Mail\ComposeMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Html2Text\Html2Text;
use Illuminate\Http\File;


class MailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(MailsDataTable $dataTable, $id="")
    {
		$folder = $id;
		$template = app();
		switch($folder)
		{
			case 'inbox':
				$template->icon = '<i class="fa fa-inbox"></i>';
				$template->title = 'Inbox';
				$folder = 'inbox';
			break;
			case 'archive':
				$template->icon = '<i class="fa fa-archive"></i>';
				$template->title = 'Archive';
				$folder = 'archive';
			break;
			case 'sent':
				$template->icon = '<i class="fa fa-envelope-o"></i>';
				$template->title = 'Sent';
				$folder = 'sent';
			break;
			case 'draft':
				$template->icon = '<i class="fa fa-file-text-o"></i>';
				$template->title = 'Draft';
				$folder = 'draft';
			break;
			case 'junk':
				$template->icon = '<i class="fa fa-filter"></i>';
				$template->title = 'Junk';
				$folder = 'junk';
			break;
			case 'trash':
				$template->icon = '<i class="fa fa-trash-o"></i>';
				$template->title = 'Trash';
				$folder = 'trash';
			break;
			default:
				$template->icon = '<i class="fa fa-inbox"></i>';
				$template->title = 'Inbox';
				$folder = 'inbox';
		}
		
        return $dataTable->with('folder',$folder)->render('mail::mails.index', compact('folder','template'));
    }
	
	
	

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$mail_accounts = Mail_Account::where('user_id',Auth::user()->id)->orderBy('name','asc')->get();
        return view('mail::mails.create')->with('mail_accounts',$mail_accounts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		if($request->input('request')=='Send' || $request->input('request')=='Draft')
		{	
			$validator = Validator::make($request->all(), [
          		'mail_from' => 'required',
				'mail_to' => 'required',
				'mail_subject' => 'required',
       		]);
			if ($validator->fails()) {
            	$errors = $validator->errors();
				return response()->json($errors);
       		}
			
			
			
			$mail_from = $request->input('mail_from');
			$mail_to = $request->input('mail_to');
			$mail_subject = $request->input('mail_subject');
			$mail_content = $request->input('mail_content');
			$mail_attachments = $request->file('mail_attachments');
			$mail_old_attachments = $request->input('mail_old_attachments');
			
			
			
			$mail_account = Mail_Account::where('user_id',Auth::user()->id)->findOrFail($mail_from);
			$mail_body_html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
							   <html xmlns="http://www.w3.org/1999/xhtml">
								<head>
									<meta name="viewport" content="width=device-width, initial-scale=1.0" />
									<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
									<title>'. $mail_subject .'</title>
								</head>
								<body>'. $mail_content .'</body>
							   </html>';
			$html = new Html2Text($mail_content);
			$mail_body_plain = $html->getText();
			
			$mail_email = new Mail_Email();
			$mail_email->user_id = Auth::user()->id;
			$mail_email->recipient = $mail_to;
			$mail_email->sender = $mail_account->email;
			$mail_email->from = $mail_account->name ."<". $mail_account->email .">";
			$mail_email->subject = $mail_subject;
			$mail_email->body_plain = $mail_body_plain;
			$mail_email->stripped_text = $mail_body_plain;
			$mail_email->body_html = $mail_body_html;
			$mail_email->stripped_html = $mail_body_html;

			$attachment_count = null;
			$old_attachment_count = null;

			if(!empty($mail_attachments))
			{
				$attachment_count = count($mail_attachments);
			}
			if(!empty($mail_old_attachments))
			{
				$old_attachment_count = count($mail_old_attachments);
			}
			$mail_email->attachment_count = $attachment_count + $old_attachment_count;
			$mail_email->attachment_x = $attachment_count + $old_attachment_count;
			
			$mail_email->timestamps = Carbon::now()->toDateTimeString();
			$mail_email->read = 1;
			$mail_email->folder = 'draft';
			$mail_email->save();
			

			
			if(!empty($attachment_count))
			{
				foreach($mail_attachments as $mail_attachment)
            	{
					$mail_attachment_name = $mail_attachment->getClientOriginalName();
					$mail_attachment_mimetype = $mail_attachment->getClientMimeType();
					$mail_attachment_size = $mail_attachment->getSize();
					$mail_attachment_path = $mail_attachment->getRealPath();
				
					$upload = MailHelper::upload_attachment($mail_attachment);
					$path = $upload['public_id'];
					$url = $upload['secure_url'];
					
					$mail_attachment_ = new Mail_Attachment();
					$mail_attachment_->email_id = $mail_email->id;
					$mail_attachment_->file_path = $path;
					$mail_attachment_->file_url = $url;
					$mail_attachment_->file_name = $mail_attachment_name;
					$mail_attachment_->file_mimetype = $mail_attachment_mimetype;
					$mail_attachment_->file_size = $mail_attachment_size;
					$mail_attachment_->save();
				}
			}
			
			
			
			if(!empty($old_attachment_count))
			{
				foreach($mail_old_attachments as $mail_old_attachment)
            	{
					
					$mail_old_attachment_ = Mail_Attachment::find($mail_old_attachment);
					
					$path = "../storage/logs/". $mail_old_attachment;
					file_put_contents($path, file_get_contents($mail_old_attachment_->file_url));
					
					$upload = MailHelper::upload_attachment($path);
					$path = $upload['public_id'];
					$url = $upload['secure_url'];
					
					$mail_attachment_ = new Mail_Attachment();
					$mail_attachment_->email_id = $mail_email->id;
					$mail_attachment_->file_path = $path;
					$mail_attachment_->file_url = $url;
					$mail_attachment_->file_name = $mail_old_attachment_->file_name;
					$mail_attachment_->file_mimetype = $mail_old_attachment_->file_mimetype;
					$mail_attachment_->file_size = $mail_old_attachment_->file_size;
					$mail_attachment_->save();
				}
			}
			
			
			
			$message = route('mails.index') .'/folder/draft';
			if($request->input('request')=='Send')
			{
				Mail::send(new ComposeMail($mail_account->name,$mail_account->email,$mail_to,$mail_subject,$mail_body_html,$mail_body_plain,$mail_attachments,$mail_old_attachments));
				$mail_email->folder = 'sent';
				$mail_email->save();
				$message = route('mails.index') .'/folder/sent';
			}
			
			if(!empty($old_attachment_count))
			{
				foreach($mail_old_attachments as $mail_old_attachment)
            	{
					if (file_exists("../storage/logs/". $mail_old_attachment)) {
						unlink("../storage/logs/". $mail_old_attachment);
					}
				}
			}
			
			return response()->json([
				'id' => '1', 'message' => $message
			]);
		}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,$view="")
    {
		$mail = Mail_Email::where('user_id',Auth::user()->id)->findOrFail($id);
		$mail->read = 1;
		$mail->save();
		if($view=="")
		{
			return view('mail::mails.show')->with('mail',$mail);
		}
		else
		{
			if(preg_replace('/\s/', '', $mail->body_html)=="")$mail->body_html = nl2br(strip_tags($mail->body_plain));
			if((strpos($mail->body_html, '</html>') === false))
			{
				$mail->body_html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
							   <html xmlns="http://www.w3.org/1999/xhtml">
								<head>
									<meta name="viewport" content="width=device-width, initial-scale=1.0" />
									<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
									<title>'. $mail->subject .'</title>
								</head>
								<body>'. $mail->body_html .'</body>
							   </html>';
			}
			return view('mail::mails.show-no-layout')->with('mail',$mail)
											  ->with('view',$view);
		}
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
		$request = $request->input('request');
		$template = app();
		switch($request)
		{
			case 'reply':
				$request = 'reply';
				$template->title = ucwords($request) .' Message';
			break;
			case 'forward':
				$request = 'forward';
				$template->title = ucwords($request) .' Message';
			break;
			default:
				$request = 'reply';
				$template->title = ucwords($request) .' Message';
		}
		
        $mail_accounts = Mail_Account::where('user_id',Auth::user()->id)->orderBy('name','asc')->get();
		$mail_email = Mail_Email::where('user_id',Auth::user()->id)->findOrFail($id);
		
		if($request=='reply')
		{
			$mail_recipient = MailHelper::get_email_from($mail_email->from);
			$mail_sender = $mail_email->recipient;
			$mail_email->subject = "Re: ". $mail_email->subject;
			$mail_email->sender = $mail_sender;
			$mail_email->recipient = $mail_recipient;
			$mail_email->body_plain = "On ". $mail_email->created_at .", ". $mail_email->from ." wrote: <br />&gt; ". str_replace("<br />","<br />&gt;",nl2br($mail_email->body_plain));
		}
		if($request=='forward')
		{
			$mail_email->body_plain = '---------- Forwarded message ---------
								      <br />
									  From: '. $mail_email->sender .'<br />
									  Date: '. Carbon::now() .'<br />
									  Subject: '.$mail_email->subject.'<br />
          							  To: '. $mail_email->recipient .'<br />'. 
									  nl2br($mail_email->body_plain);
    		$mail_email->sender = $mail_email->recipient;
			$mail_email->recipient = "";
			$mail_email->subject = "Fwd: ". $mail_email->subject;
		}
        return view('mail::mails.edit')->with('mail_accounts',$mail_accounts)
								->with('mail_email',$mail_email)
								->with('template',$template);
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
        if($request->input('request')=='change_status_read')
		{
			$read = $request->input('read');
			$mail = Mail_Email::where('user_id',Auth::user()->id)->findOrFail($id);
			$mail->read = $read;
			$mail->save();
			return response()->json([
				'id' => '1', 'message' => 'change_status_read success'
			]);
		}
		if($request->input('request')=='move_to_trash_selected')
		{
			$array_id = explode(",",$id);
			for($i=0;$i<count($array_id)-1;$i++)
			{
				$mail = Mail_Email::where('user_id',Auth::user()->id)->findOrFail($array_id[$i]);
				$mail->folder = 'trash';
				$mail->save();
			}
			return response()->json([
				'id' => '1', 'message' => 'move_to_trash_selected success'
			]);
		}
		if($request->input('request')=='move_to_trash')
		{
			$mail = Mail_Email::where('user_id',Auth::user()->id)->findOrFail($id);
			$mail->folder = 'trash';
			$mail->save();
			return response()->json([
				'id' => '1', 'message' => 'move_to_trash success'
			]);	
		}
		if($request->input('request')=='move_to_archive_selected')
		{
			$array_id = explode(",",$id);
			for($i=0;$i<count($array_id)-1;$i++)
			{
				$mail = Mail_Email::where('user_id',Auth::user()->id)->findOrFail($array_id[$i]);
				$mail->folder = 'archive';
				$mail->save();
			}
			return response()->json([
				'id' => '1', 'message' => 'move_to_archive_selected success'
			]);
		}
		if($request->input('request')=='move_to_archive')
		{
			$mail = Mail_Email::where('user_id',Auth::user()->id)->findOrFail($id);
			$mail->folder = 'archive';
			$mail->save();
			return response()->json([
				'id' => '1', 'message' => 'move_to_archive success'
			]);	
		}
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
		if($request->header('request')=='delete_all')
		{
			$mails = Mail_Email::where('user_id',Auth::user()->id)->where('folder',$id)->get();
			foreach($mails as $mail)
			{
				foreach($mail->mail_attachments as $attachment)
				{
					MailHelper::destroy_attachment($attachment->file_path);
				}
				$mail->mail_attachments()->delete();
				$mail->delete();
			}
			
		}
        if($request->header('request')=='delete_forever')
		{
			
			$mail = Mail_Email::where('user_id',Auth::user()->id)->findOrFail($id);
			foreach($mail->mail_attachments as $attachment)
			{
				MailHelper::destroy_attachment($attachment->file_path);
			}
			
			$mail->mail_attachments()->delete();
			$mail->delete();
			return response()->json([
				'id' => '1', 'message' => 'delete_forever success'
			]);	
		}
		if($request->header('request')=='delete_forever_selected')
		{
			$array_id = explode(",",$id);
			for($i=0;$i<count($array_id)-1;$i++)
			{
				$mail = Mail_Email::where('user_id',Auth::user()->id)->findOrFail($array_id[$i]);
				foreach($mail->mail_attachments as $attachment)
				{
					MailHelper::destroy_attachment($attachment->file_path);
				}
				$mail->mail_attachments()->delete();
				$mail->delete();
			}
			return response()->json([
				'id' => '1', 'message' => 'delete_forever_selected success'
			]);
		}
    }
}
