{!! $mail->body_html !!}
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