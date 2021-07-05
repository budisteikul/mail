<script language="javascript">
function STORE()
{
	var error = false;
	var input = ["name", "email"];
	
	$("#submit").attr("disabled", true);
	$("#submit").html('<i class="fa fa-spinner fa-spin"></i>');
	
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
		$("#submit").attr("disabled", false);
		$("#submit").html('<span class="fa fa-save"></span> {{ __('Save') }}');
		return false;	
	}
	
	if(!error)
	{
		var table = $('#dataTableBuilder').DataTable();
		$.ajax({
			data: {
        		"_token": $("meta[name=csrf-token]").attr("content"),
        		"name": $('#name').val(),
				"email": $('#email').val()
        	},
			type: 'POST',
			url: '{{ route('mail_settings.index') }}'
			}).done(function( data ) {
			if(data=="")
			{
				table.ajax.reload( null, false );
				$.fancybox.close();
			}
			else
			{
				$.each( data, function( key, value ) {
						$('#div-'+ key).removeClass('form-group').addClass('form-group has-error');
						$('#div-'+ key).prepend('<label id="label-'+ key +'" class="control-label" for="'+ key +'"><i class="fa fa-times-circle-o"></i> '+ value +'</label>');
						$('#span-'+ key).removeClass('input-group-addon').addClass('input-group-addon has-error');
				});	
				$("#submit").attr("disabled", false);
				$("#submit").html('<span class="fa fa-save"></span> {{ __('Save') }}');
			}
		});	
	}
	
	return false;
}
</script>

<div class="box box-primary col-md-12" style="height:100%">
	<div id="result" class="box-header with-border">
    <h3 class="box-title text-light-blue"><i class="fa fa-user"></i> Add account</h3>     	
	</div>
	<div class="box-body">
		<form onSubmit="return STORE()">
        <div class="form-group row">
			<label for="name" class="col-sm-1 col-form-label">{{ __('Name') }}</label>
				<div class="col-sm-8">
					<div id="div-name" class="form-group">
			<input type="text" id="name" name="name" class="form-control" placeholder="{{ __('Name') }}" autocomplete="off">
					</div>
				</div>
		</div>
        
		<div class="form-group row">
			<label for="email" class="col-sm-1 col-form-label">{{ __('Email') }}</label>
				<div class="col-sm-8">
					<div id="div-email" class="form-group">
			<input type="email" id="email" name="email" class="form-control" placeholder="{{ __('Email') }}" autocomplete="off">
					</div>
                    <button onClick="$.fancybox.close();" class="btn btn-danger btn-flat" id="cancel" type="button" name="cancel" value="Cancel"><span class="fa fa-close"></span> {{ __('Cancel') }}</button>
                    <button class="btn btn-primary btn-flat" id="submit" type="submit" name="submit" value="Save"><span class="fa fa-save"></span> {{ __('Save') }}</button>
				</div>
		</div>

		</form>
	</div>
	<div class="box-footer">
          
	</div>
</div>