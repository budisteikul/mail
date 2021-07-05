@inject('MailHelper', 'budisteikul\mail\Helpers\MailHelper')
@extends('mail::layouts.admin-lte.blank',['folder' => $mail->folder])
@section('title', 'Mailbox '. html_entity_decode('&raquo;') .' Show Message')
@section('content')
@push('scripts')
<script type="text/javascript">
function VIEW_HTML()
	{
		$.fancybox.open({
        	type: 'iframe',
       	 	src: '{{ route('mails.index') }}/{{ $mail->id }}/html',
			
   		});
		
	}
function DELETE()
	{
		$.confirm({
    		title: 'Warning',
    		content: 'Are you sure?',
    		type: 'red',
			icon: 'fa fa-warning',
    		buttons: {   
        		ok: {
            		text: "OK",
            		btnClass: 'btn-danger btn-flat',
            		keys: ['enter'],
            		action: function(){
						
						@if($mail->folder=='trash')
						
						
							$.ajax({
							beforeSend: function(request) {
    							request.setRequestHeader("X-CSRF-TOKEN", $("meta[name=csrf-token]").attr("content"));
  						},
     						type: 'DELETE',
     						url: '{{ route('mails.index') }}/{{ $mail->id }}',
							headers: { 'request': 'delete_forever' }
						}).done(function( msg ) {
							window.location='{{ route('mails.index') }}/?folder={{ $mail->folder }}';
						});	
						
						@else
						
						
							$.ajax({
							data: {
        						"_token"  : $("meta[name=csrf-token]").attr("content"),
        						"request" : "move_to_trash"
        					},
     						type: 'PUT',
     						url: '{{ route('mails.index') }}/{{ $mail->id }}'
						}).done(function( msg ) {
							window.location='{{ route('mails.index') }}/?folder={{ $mail->folder }}';
						});
						
						@endif
						
            		}
        		},
        		cancel: function(){
                	console.log('the user clicked cancel');
        		}
    		}
		});
		
	}
	
function ARCHIVE()
	{
		$.confirm({
    		title: 'Warning',
    		content: 'Are you sure?',
    		type: 'blue',
			icon: 'fa fa-warning',
    		buttons: {   
        		ok: {
            		text: "OK",
            		btnClass: 'btn-primary btn-flat',
            		keys: ['enter'],
            		action: function(){
							$.ajax({
							data: {
        						"_token"  : $("meta[name=csrf-token]").attr("content"),
        						"request" : "move_to_archive"
        					},
     						type: 'PUT',
     						url: '{{ route('mails.index') }}/{{ $mail->id }}'
						}).done(function( msg ) {
							window.location='{{ route('mails.index') }}/?folder={{ $mail->folder }}';
						});
            		}
        		},
        		cancel: function(){
                	console.log('the user clicked cancel');
        		}
    		}
		});
		
	}
</script>
@endpush
 <div class="content-wrapper">
	<section class="content-header">
      <h1>
       Mailbox
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li>Mailbox</li>
        <li>{{ Illuminate\Support\Str::title($mail->folder) }}</li>
      </ol>
    </section>
<!-- Main content -->
    <section class="content">
      <div class="row">
        
        <div class="col-md-12">
          
		  
		  
		  
		  <div class="box box-primary">
            <!-- div class="box-header with-border">
              <h3 class="box-title">Read Mail</h3>

              
            </div -->
            <!-- /.box-header -->
            <div class="box-body no-padding">
              <div class="mailbox-read-info">
                <h3>{{ $mail->subject }}</h3>
                <h5>
                <strong>From:</strong> {{ $mail->from }}<br />
                <strong>To:</strong> {{ $mail->recipient }}
                  <span class="mailbox-read-time pull-right">
				  {{ Carbon\Carbon::parse($mail->created_at)->formatLocalized('%d %b. %Y %I:%M %p') }}
				  
				  </span></h5>
              </div>
              <!-- /.mailbox-read-info -->
              
              <!-- /.mailbox-controls -->
              <div class="table-responsive mailbox-read-message">
			   {!! nl2br(strip_tags($mail->body_plain)) !!}
              </div>
              <!-- /.mailbox-read-message -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
              <ul class="mailbox-attachments clearfix">
                @foreach($mail->mail_attachments as $attachment)
				<li>
                  
				  @if((strpos($attachment->file_mimetype, 'image') !== false))
                  <span class="mailbox-attachment-icon has-img"><img src="{{ $attachment->file_url }}" alt=""></span>
                  @elseif((strpos($attachment->file_mimetype, 'pdf') !== false))
                  <span class="mailbox-attachment-icon"><i class="fa fa-file-pdf-o"></i></span>
                  @elseif((strpos($attachment->file_mimetype, 'excel') !== false))
                  <span class="mailbox-attachment-icon"><i class="fa fa-file-excel-o"></i></span>
                  @elseif((strpos($attachment->file_mimetype, 'presentation') !== false))
                  <span class="mailbox-attachment-icon"><i class="fa fa-file-powerpoint-o"></i></span>
                  @elseif((strpos($attachment->file_mimetype, 'word') !== false))
                  <span class="mailbox-attachment-icon"><i class="fa fa-file-word-o"></i></span>
                  @elseif((strpos($attachment->file_mimetype, 'zip') !== false))
                  <span class="mailbox-attachment-icon"><i class="fa fa-file-zip-o"></i></span>
				  @else
				  <span class="mailbox-attachment-icon"><i class="fa fa-file-o"></i></span>	  
				  @endif
                  <div class="mailbox-attachment-info">
                    <a href="{{ route('mail_attachments.show',['attachment'=>$attachment->id]) }}" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i> {{ $attachment->file_name }}</a>
                        <span class="mailbox-attachment-size">
                          {{ $MailHelper->bytesToHuman($attachment->file_size) }}
                          <a href="{{ route('mail_attachments.show',['attachment'=>$attachment->id]) }}" class="btn btn-default btn-xs pull-right"><i class="fa fa-cloud-download"></i></a>
                        </span>
                  </div>
                </li>
				@endforeach
                
              </ul>
            </div>
            <!-- /.box-footer -->
            <div class="box-footer">
              <div class="pull-right">
                <button type="button" class="btn btn-default btn-flat" onClick="window.location.href='{{ route('mails.edit',['mail'=>$mail->id]) }}/?request=reply'"><i class="fa fa-reply"></i> Reply</button>
                <button type="button" class="btn btn-default btn-flat" onClick="window.location.href='{{ route('mails.edit',['mail'=>$mail->id]) }}/?request=forward'"><i class="fa fa-share"></i> Forward</button>
              </div>
              <button type="button" class="btn btn-default btn-flat" onClick="return VIEW_HTML()"><i class="fa fa-html5"></i> View HTML</button>
              @if($mail->folder=='trash')
              <button type="button" class="btn btn-default btn-flat" onClick="return DELETE()"><i class="fa fa-trash-o"></i> Delete Forever</button>
              @else
              <button type="button" class="btn btn-default btn-flat" onClick="return DELETE()"><i class="fa fa-trash-o"></i> Delete</button>
              @endif
              <button type="button" class="btn btn-default btn-flat" onClick="return ARCHIVE()"><i class="fa fa-archive"></i> Archive</button>
              <button type="button" class="btn btn-default btn-flat" onClick="return PRINT('{{ route('mails.index') }}/{{ $mail->id }}/print')"><i class="fa fa-print"></i> Print</button>
            </div>
            <!-- /.box-footer -->
          </div>
		  <script type="text/javascript">
          function PRINT(link) {
        	$("<iframe class='printpage'>")
            	.attr("src", link)
            	.appendTo("body");
   			 }
          </script>
		  
		  
		  
		  
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
	</div>

	@endsection