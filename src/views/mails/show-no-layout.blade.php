@inject('MailHelper', 'budisteikul\mail\Helpers\MailHelper')
@php
	$content = $MailHelper->get_string_between($mail->body_html,'</head>','</html>');
	if($content==""){
		$content = $MailHelper->get_string_between($mail->body_html,'<body>','</body>');
	}
	$content = str_ireplace("<body","<div",$content);
	$content = str_ireplace("<body>","<div>",$content);
	$content = str_ireplace("</body>","</div>",$content);
	
@endphp


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<meta name="viewport" content="width=device-width, initial-scale=1.0" />
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<title>{{$mail->subject}}</title>
		</head>
	<body>
		<div class="mailbox-read-info">
                <h3>{{ $mail->subject }}</h3>
                
                <strong>From:</strong> {{ $mail->from }}<br />
                <strong>To:</strong> {{ $mail->recipient }}
                  <span class="mailbox-read-time pull-right">
				  {{ Carbon\Carbon::parse($mail->created_at)->formatLocalized('%d %b. %Y %I:%M %p') }}
				  
				  </span>
              </div>
	<hr>
	{!! $content !!}
	<hr>
		<ul>
			@foreach($mail->mail_attachments as $attachment)
			<li>
            	<a href="{{ route('mail_attachments.show',['attachment'=>$attachment->id]) }}">{{ $attachment->file_name }}</a>
				<span>
					{{ $MailHelper->bytesToHuman($attachment->file_size) }}
				</span>
			</li>
			@endforeach
		</ul>
	<div style="height: 30px;"></div>
@if($view=="print")
<script src="{{asset('js/admin-lte3.js')}}"></script>
<script type="text/javascript">
		$(document).ready(function() {
			window.print();
		});
		window.onafterprint = function () {
        	$('.printpage', window.parent.document).hide();
    	}
</script>
@else
<script src="{{asset('js/admin-lte3.js')}}"></script>
<script type="text/javascript">
		$(document).ready(function() {
  			$("a[href^='http']").attr('target','_parent');
		});
</script>
@endif    


	</body>
	</html>



