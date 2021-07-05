@extends('mail::layouts.admin-lte.blank',['folder' => 'create'])
@section('title', 'Mailbox '. html_entity_decode('&raquo;') .' Compose New Message')
@section('content')
@push('scripts')
<script>
  $(function () {
    //Add text editor
    $("#mail_content").wysihtml5();
	
	$(document).on('click','.clear-file',function(){
		$(this).closest('span').prev().val("");
		$(this).closest('button').hide();
		$(this).parent().find('input').val("");
		$(this).parent().find('input').prev().text("Browse");
    });
	$(document).on('change',".file_input", function (){
        var file = this.files[0];
		$(this).prev().text("Change");
		$(this).closest('div').prev().show();
		$(this).closest('div').prev().closest('span').prev().val(file.name);
	});
	
	$('#add-new-attachment').on('click', function(){
    var input = '<div class="col-md-11 col-xs-10 no-padding"><div id="div-file" class="form-group"><div class="input-group"><input type="text" id="filename-file" class="form-control" disabled="disabled"><span class="input-group-btn"><button type="button" class="clear-file btn btn-default" style="display:none;"><span class="glyphicon glyphicon-remove"></span> Clear</button><div id="input-file" class="btn btn-default upload-input" style=""><span class="glyphicon glyphicon-folder-open"></span><span id="title-file" class="upload-input-title"> Browse</span><input id="mail_attachments" type="file" class="file_input" name="mail_attachments[]"/></div></span></div></div></div><div><button type="button" class="btn btn-danger btn-flat deleteFileBox"><i class="fa fa-trash-o"></i></button></div><div class="clearfix"></div>';
    $('#div-attachments').append(input);
  });

  $(document).on('click', '.deleteFileBox', function(){
	$(this).parent().prev().closest('div').remove();
	$(this).remove();
  });
	
  });
</script>
<script language="javascript">
function SEND(request)
{
	var error = false;
	var input = ['mail_from', "mail_to", "mail_subject"];
	
	if(request=="Send")
	{
		var icon = '<i class="fa fa-envelope-o"></i>';
	}
	else
	{
		var icon = '<i class="fa fa-pencil"></i>';
	}
	
	$("#submit_"+ request).attr("disabled", true);
	$("#submit_"+ request).html('<i class="fa fa-spinner fa-spin"></i>');
	
	$.each(input, function( index, value ) {
  		$('#label-'+ value).remove();
		$('#div-'+ value).removeClass('form-group has-error').addClass('form-group');
		$('#span-'+ value).removeClass('input-group-addon has-error').addClass('input-group-addon');
	});
	
	
	$.each(input, function( index, value ) {
		if($('#'+ value).val()=="" || $('#'+ value).val()==null)
		{
			$('#div-'+ value).removeClass('form-group').addClass('form-group has-error');
			$('#div-'+ value).prepend('<label id="label-'+ value +'" class="control-label" for="'+ value +'"><i class="fa fa-times-circle-o"></i> The '+ $('#'+ value).attr('placeholder') +' field is required.</label>');
			$('#span-'+ value).removeClass('input-group-addon').addClass('input-group-addon has-error');
			error = true;
		}
	});
	
	
	if(error)
	{
		$("#submit_"+ request).attr("disabled", false);
		$("#submit_"+ request).html(icon +' '+ request);
		return false;	
	}
	
	if(!error)
	{
		var formData = new FormData();
		formData.append("request",request);
		formData.append("mail_from",$('#mail_from').val());
		formData.append("mail_to",$('#mail_to').val());
		formData.append("mail_subject",$('#mail_subject').val());
		formData.append("mail_content",$('#mail_content').val());
		formData.append("_token",$("meta[name=csrf-token]").attr("content"));
		
		for (var x = 0; x < $("input[class*='file_input']").length; x++) {
			formData.append("mail_attachments[]",$("input[class*='file_input']")[x].files[0]);
		}
			
		$.ajax({
			data : formData,
			type: 'POST',
			processData: false,
			contentType: false,
			url: '{{ route('mails.index') }}'
			}).done(function( data ) {
			if(data.id=="1")
			{
				window.location.href = data.message; 
			}
			else
			{
				$.each( data, function( key, value ) {
						$('#div-'+ key).removeClass('form-group').addClass('form-group has-error');
						$('#div-'+ key).prepend('<label id="label-'+ key +'" class="control-label" for="'+ key +'"><i class="fa fa-times-circle-o"></i> '+ value +'</label>');
						$('#span-'+ key).removeClass('input-group-addon').addClass('input-group-addon has-error');
				});	
				$("#submit_"+ request).attr("disabled", false);
				$("#submit_"+ request).html(icon +' '+ request);
			}
		});	
	}
	
	return false;
}
</script>
@endpush
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Mailbox
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('mails.index') }}">Mailbox</a></li>
        <li>Compose</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        
        <!-- /.col -->
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Compose New Message</h3>
              <div class="box-tools pull-right ">
                <div class="has-feedback" style="margin-top:5px;">
                  <a href="{{ route('mail_settings.index') }}" class="text-muted"><i class="fa fa-gear"></i></a>
                </div>
              </div>
            </div>
            <!-- /.box-header -->
            <form onSubmit="return SEND('Send')">
            
            <div class="box-body">
			
              <div id="div-mail_from" class="form-group">
                  <label>From:</label>
                  <select id="mail_from" name="mail_from" class="form-control" placeholder="From:">
                  @foreach($mail_accounts as $mail_account)
                    <option value="{{ $mail_account->id }}">{{ $mail_account->name }} &lt;{{ $mail_account->email }}&gt; </option>
                  @endforeach
                  </select>
                </div>
              <div id="div-mail_to" class="form-group">
                <input class="form-control" name="mail_to" id="mail_to" placeholder="To:">
              </div>
              <div id="div-mail_subject" class="form-group">
                <input class="form-control" name="mail_subject" id="mail_subject" placeholder="Subject:">
              </div>
              <div id="div-mail_content" class="form-group">
                    <textarea id="mail_content" name="mail_content" class="form-control" style="height: 300px"></textarea>
              </div>
			  
              <div id="div-attachments">
              
             
              </div>
             
             <div>
                <span class="small"><a id="add-new-attachment" class="btn btn-success btn-flat"><i class="fa fa-paperclip"></i> Attachment</a></span>
             </div>               
							
            </div>
			
            <!-- /.box-body -->
            <div class="box-footer">
              <div class="pull-right">
                <button id="submit_Draft" type="button" onClick="return SEND('Draft')" class="btn btn-default btn-flat"><i class="fa fa-pencil"></i> Draft</button>
                <button id="submit_Send" type="submit" class="btn btn-primary btn-flat"><i class="fa fa-envelope-o"></i> Send</button>
              </div>
              <button type="button" onClick="window.location='{{ route('mails.index') }}'" class="btn btn-default btn-flat"><i class="fa fa-times"></i> Discard</button>
            </div>
            <!-- /.box-footer --><!-- /. box -->
          </form>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
@endsection