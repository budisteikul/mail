@extends('mail::layouts.admin-lte.blank')
@section('title', 'Mailbox '. html_entity_decode('&raquo;') .' '. $template->title)
@section('content')
@push('scripts')
<script type="text/javascript">
function CHANGE(id,status)
{
	var table = $('#dataTableBuilder').DataTable();
	$.ajax({
		data: {
			"_token"  : "{{ csrf_token() }}",
			"request" : "change_status_read",
			"read" 	  : status
		},
		type: 'PUT',
		url: '{{ route('mails.index') }}/'+ id
		}).done(function( msg ) {
			table.ajax.reload( null, false );
		});
	return false;
}
</script>
<script type="text/javascript">

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
						
						var table = $('#dataTableBuilder').DataTable();
						var checkbox_cookies = Cookies.get('checkbox_id');
						
						if(checkbox_cookies=="")
						{
							table.ajax.reload( null, false );
						}
						else
						{
							
							$.ajax({
								data: {
									"_token"  : $("meta[name=csrf-token]").attr("content"),
									"request" : "move_to_archive_selected"
								},
								type: 'PUT',
								url: '{{ route('mails.index') }}/'+ checkbox_cookies
							}).done(function( msg ) {
								RESTART_CHECKBOX();
								table.ajax.reload( null, false );
							});
							
						}
						
						
            		}
        		},
        		cancel: function(){
                	console.log('the user clicked cancel');
					
        		}
    		}
		});
		
	}
</script>
<script type="text/javascript">

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
						
						var table = $('#dataTableBuilder').DataTable();
						var checkbox_cookies = Cookies.get('checkbox_id');
						
						if(checkbox_cookies=="")
						{
							table.ajax.reload( null, false );
						}
						else
						{
							@if($folder=="trash")
							$.ajax({
							beforeSend: function(request) {
    							request.setRequestHeader("X-CSRF-TOKEN", '{{csrf_token()}}');
  							},
     							type: 'DELETE',
     							url: '{{ route('mails.index') }}/'+ checkbox_cookies,
								headers: { 'request': 'delete_forever_selected' }
							}).done(function( msg ) {
								RESTART_CHECKBOX();
								table.ajax.reload( null, false );
							});	
							@else
							$.ajax({
								data: {
									"_token"  : $("meta[name=csrf-token]").attr("content"),
									"request" : "move_to_trash_selected"
								},
								type: 'PUT',
								url: '{{ route('mails.index') }}/'+ checkbox_cookies
							}).done(function( msg ) {
								RESTART_CHECKBOX();
								table.ajax.reload( null, false );
							});
							@endif
						}
						
						
            		}
        		},
        		cancel: function(){
                	console.log('the user clicked cancel');
					
        		}
    		}
		});
		
	}


function EMPTY()
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
						var checkbox_cookies = Cookies.get('checkbox_id');
						
						
							$.ajax({
							beforeSend: function(request) {
    							request.setRequestHeader("X-CSRF-TOKEN", '{{csrf_token()}}');
  							},
     							type: 'DELETE',
     							url: '{{ route('mails.index') }}/{{ $folder }}',
								headers: { 'request': 'delete_all' }
							}).done(function( msg ) {
								RESTART_CHECKBOX();
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
</script>
		<script type="text/javascript">
		$(document).ready(function() {
			RESTART_CHECKBOX();
		});
		
		
		
		
		function RELOAD_MAIL()
		{
			
			var table = $('#dataTableBuilder').DataTable();
			table.ajax.reload( null, false );
			
		}
		
		function RELOAD_CHECKBOX()
		{
			var checkbox_cookies = Cookies.get("checkbox_id");
			var myStringArray = checkbox_cookies.split(",");
			var arrayLength = myStringArray.length;
			for (var i = 0; i < arrayLength-1; i++) {
    			$("#checkbox_"+ myStringArray[i]).attr("checked", true);
			}
			
			CHECK_STATE();
		}
		
		function RESTART_CHECKBOX()
		{
			Cookies.remove('checkbox_id');
			if(Cookies.get('checkbox_id')==null)
			{
					Cookies.set('checkbox_id', '', { expires: 7 });
			}	
		}
		
		
		
		function SELECTALL_CHECKBOX()
		{
			if($('#check_all').attr('class').indexOf("fa-check-square-o") >= 0)
			{
				
				$('.icheckbox').each(function() {
					if($("#"+ this.id).is(':checked'))
					{
						$('#'+ this.id).iCheck('uncheck');
						var checkbox_cookies = Cookies.get('checkbox_id');
						checkbox_cookies = checkbox_cookies.replace(this.value+',','');
						Cookies.set('checkbox_id', checkbox_cookies, { expires: 7 });
					}
				});
				
				
			}
			
			if($('#check_all').attr('class').indexOf("fa-square-o") >= 0)
			{
				
				$('.icheckbox').each(function() {
					if(!$("#"+ this.id).is(':checked'))
					{
						
						//$('#'+ this.id).attr('checked','true');
						$('#'+ this.id).iCheck('check');
						var checkbox_cookies = Cookies.get('checkbox_id');
						checkbox_cookies = checkbox_cookies.replace(this.value+',','');
						Cookies.set('checkbox_id', checkbox_cookies + this.value +',', { expires: 7 });
					}
				});
				
				
			}
			
			CHECK_STATE();
		}
		
		function CHECK_STATE()
		{
			var i = 0;
			var j = 0;
			$('.icheckbox').each(function() {
				i = i + 1;
				if($("#"+ this.id).is(':checked'))
				{
					j = j + 1;
				}
			});
			
			if(i==j && i > 0)
			{
				$("#check_all").removeClass("fa fa-square-o").addClass("fa fa-check-square-o");	
			}
			else
			{
				$("#check_all").removeClass("fa fa-check-square-o").addClass("fa fa-square-o");	
			}
		}
		
		function SET_CHECKBOX(name,id)
		{
			var checkbox_cookies = Cookies.get('checkbox_id');
			
			if($("#"+ name).is(':checked'))
			{		
				$('#'+ name).iCheck('uncheck');
			}
			else
			{		
				$('#'+ name).iCheck('check');
			}
			
			var checkbox_cookies = Cookies.get('checkbox_id');
			
			if($("#"+ name).is(':checked'))
			{
				checkbox_cookies = checkbox_cookies.replace(id+',','');
				Cookies.set('checkbox_id', checkbox_cookies + id +',', { expires: 7 });
			}
			else
			{
				checkbox_cookies = checkbox_cookies.replace(id+',','');
				Cookies.set('checkbox_id', checkbox_cookies, { expires: 7 });
			}
			CHECK_STATE();
		}
		</script>
        @endpush
 <div class="content-wrapper">
	<section class="content-header">
      <h1>
        Mailbox
      </h1>
      <ol class="breadcrumb">
        <li><a href="/mails"><i class="fa fa-dashboard"></i> Home</a></li>
        <li>Mailbox</li>
      	<li>{{ $template->title }}</li>
      </ol>
    </section>
<!-- Main content -->
    <section class="content">
      <div class="row">
        
        <!-- /.col -->
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">{!! $template->icon !!}  {{ $template->title }}</h3>

              <div class="box-tools pull-right ">
              
                <div class="has-feedback" style="margin-top:5px;">
                  
                  <a href="{{ route('mail_settings.index') }}" class="text-muted"><i class="fa fa-gear"></i></a>
                </div>
                
              </div>
              <!-- /.box-tools -->
            </div>
            
            <!-- /.box-header -->
            <div class="box-body">
			
			
				
			
           		
                <!-- /.pull-right -->
              
	
					
					
					
				{!! $dataTable->table(['class'=>'table table-striped table-hover']) !!}
                <!-- /.table -->
              
              <!-- /.mail-box-messages -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer no-padding">
              <div class="mailbox-controls">
              </div>  
            </div>
          </div>
          <!-- /. box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
	</div>

	{!! $dataTable->scripts() !!}
	@endsection