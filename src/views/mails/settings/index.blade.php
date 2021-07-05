@extends('mail::layouts.admin-lte.blank',['folder' => 'setting'])
@section('title', 'Mailbox '. html_entity_decode('&raquo;') .' Settings')
@section('content')
@push('scripts')
<script type="text/javascript">
	function DELETE(id)
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
                 		var table = $('#dataTableBuilder').DataTable();
							$.ajax({
							beforeSend: function(request) {
    							request.setRequestHeader("X-CSRF-TOKEN", $("meta[name=csrf-token]").attr("content"));
  						},
     						type: 'DELETE',
     						url: '{{ route('mail_settings.index') }}/'+ id
						}).done(function( msg ) {
							table.ajax.reload( null, false );
						});	
            		}
        		},
        		cancel: function(){
                	console.log('the user clicked cancel');
        		}
    		}
		});
		
	}
	
	function CREATE()
	{
		$.fancybox.open({
        	type: 'ajax',
       	 	src: '{{ route('mail_settings.create') }}',
   		});	
	}
	
	function EDIT(id)
	{
		
		$.fancybox.open({
        	type: 'ajax',
       	 	src: '{{ route('mail_settings.index') }}/'+ id +'/edit'
   		});
		
	}
	
	function UPDATE_NOTIF(id,status)
	{
		var table = $('#dataTableBuilder').DataTable();
		$.ajax({
     		data: {
        		"_token": $("meta[name=csrf-token]").attr("content"),
        		"status": status,
				"option": "option"
        	},
     		type: 'PUT',
     		url: '{{ route('mail_settings.index') }}/'+ id
			}).done(function( data ) {
				console.log(data);
				table.ajax.reload( null, false );
			});	
	}
	</script>
<script language="javascript">
function STORE_OPTION()
{
	var error = false;
	var input = ["pushover_app","pushover_key"];
	
	$("#submit_option").attr("disabled", true);
	$("#submit_option").html('<i class="fa fa-spinner fa-spin"></i>');
	
	$.each(input, function( index, value ) {
  		$('#label-'+ value).remove();
		$('#div-'+ value).removeClass('form-group has-error').addClass('form-group');
		$('#span-'+ value).removeClass('input-group-addon has-error').addClass('input-group-addon');
	});
	
	
	if(error)
	{
		$("#submit_option").attr("disabled", false);
		$("#submit_option").html('<span class="fa fa-save"></span> {{ __('Save') }}');
		return false;	
	}
	
	if(!error)
	{
		var table = $('#dataTableBuilder').DataTable();
		$.ajax({
			data: {
        		"_token": $("meta[name=csrf-token]").attr("content"),
        		"pushover_app": $('#pushover_app').val(),
				"pushover_user": $('#pushover_user').val(),
				"option": 'option'
        	},
			type: 'POST',
			url: '{{ route('mail_settings.index') }}'
			}).done(function( data ) {
			if(data.id==1)
			{
				$("#result_option").empty().append('<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><h4><i class="icon fa fa-check"></i> Success!</h4>'+ data.message +'</div>').hide().fadeIn();
				$("#submit_option").attr("disabled", false);
				$("#submit_option").html('<span class="fa fa-save"></span> {{ __('Save') }}');
			}
			else
			{
				$.each( data, function( key, value ) {
						$('#div-'+ key).removeClass('form-group').addClass('form-group has-error');
						$('#div-'+ key).prepend('<label id="label-'+ key +'" class="control-label" for="'+ key +'"><i class="fa fa-times-circle-o"></i> '+ value +'</label>');
						$('#span-'+ key).removeClass('input-group-addon').addClass('input-group-addon has-error');
				});	
				$("#submit_option").attr("disabled", false);
				$("#submit_option").html('<span class="fa fa-save"></span> {{ __('Save') }}');
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
        Settings
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('mails.index') }}"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="{{ route('mails.index') }}">Mailbox</a></li>
        <li class="active">Settings</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Default box -->
      <div class="box">
        <div id="result" class="box-header">
         	
        </div>
        <div class="box-body">
        
          <!-- Custom Tabs -->
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab_1" data-toggle="tab">Accounts</a></li>
              <li><a href="#tab_2" data-toggle="tab">Notifications</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab_1">
              <p>
              
              
              
      <div >
        <div class="box-header">
        
        </div>
        <div class="box-body">
            <button id="btn-edit" onClick="CREATE()" type="button" class="btn btn-primary btn-flat"><span class="fa fa-plus"></span> Create </button>
        </div>
        <div class="box-body" style="min-height:300px;">
          {!! $dataTable->table(['class'=>'table table-hover']) !!}
        </div>
      </div>
      <!-- /.box -->
              
              
              
              
              
              
              </p>
              </div>
              <div class="tab-pane" id="tab_2">
              <p>
              
              
        <div >
        <div id="result_option" class="box-header">
        
        </div>
        <div class="box-body">
        <form onSubmit="return STORE_OPTION()">
        <div class="form-group row">
			<label for="pushover_app" class="col-sm-2 col-form-label">{{ __('Pushover app key') }}</label>
				<div class="col-sm-5">
					<div id="div-pushover_app" class="form-group">
			<input type="text" id="pushover_app" name="pushover_app" value="{{ $settings->pushover_app }}" class="form-control" placeholder="{{ __('Pushover app key') }}" autocomplete="off">
					</div>
				</div>
		</div>
        
		<div class="form-group row">
			<label for="pushover_user" class="col-sm-2 col-form-label">{{ __('Pushover user key') }}</label>
				<div class="col-sm-5">
					<div id="div-pushover_user" class="form-group">
			<input type="text" id="pushover_user" name="pushover_user" value="{{ $settings->pushover_user }}"  class="form-control" placeholder="{{ __('Pushover api key') }}" autocomplete="off">
					</div>
                    <button class="btn btn-primary btn-flat" id="submit_option" type="submit" name="submit" value="Save"><span class="fa fa-save"></span> {{ __('Save') }}</button>
				</div>
		</div>
		</form>
        </div>
        </div>      
              
              
              
              </p>
              </div>
            </div>
          </div>
          <!-- nav-tabs-custom -->
          
       </div>
       <div class="box-footer">
       </div>
     </div>
   </section>
</div>
{!! $dataTable->scripts() !!}
@endsection