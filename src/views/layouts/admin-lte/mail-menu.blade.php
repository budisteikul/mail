	  
      @php
        if($folder=="")
        {
            $folder = 'inbox';
        }
      @endphp


      <script language="javascript">
        $( document ).ready(function() {
            check_mail();
        });

        function check_mail()
        {
            $.ajax({
                type: 'GET',
                url: '{{ route('mails.index') }}/check'
                }).done(function( data ) {
                    $('#inboxSpan').html(data.inbox);
                    $('#junkSpan').html(data.junk);
                    $('#trashSpan').html(data.trash);
                });
        }
      </script>
      
      
      
      <div class="user-panel">
          @if($folder == "create" || $folder == "edit")
          <button class="btn btn-primary btn-block" onClick="window.location='{{ route('mails.index') }}'">Back to inbox</button>
          @else
          <button class="btn btn-primary btn-block" onClick="window.location='{{ route('mails.create') }}'">Compose New Message</button>
          @endif
        
      </div>
      
      <ul class="sidebar-menu" data-widget="tree">
      
        <li class="{{ $folder=='inbox' ? 'active' : '' }}">
        	<a href="{{ route('mails.index') }}/folder/inbox">
            	<i class="fa fa-inbox"></i> Inbox <span id="inboxSpan" class="label label-primary pull-right"></span>
            </a>
        </li>
        <li class="{{ $folder=='archive' ? 'active' : '' }}">
        	<a href="{{ route('mails.index') }}/folder/archive">
            	<i class="fa fa-archive"></i> Archive
            </a>
        </li>
        <li class="{{ $folder=='sent' ? 'active' : '' }}">
        	<a href="{{ route('mails.index') }}/folder/sent">
            	<i class="fa fa-envelope-o"></i> Sent
            </a>
        </li>
         <li class="{{ $folder=='draft' ? 'active' : '' }}">
         	<a href="{{ route('mails.index') }}/folder/draft">
            	<i class="fa fa-file-text-o"></i> Draft
            </a>
         </li>
         <li class="{{ $folder=='junk' ? 'active' : '' }}">
         	<a href="{{ route('mails.index') }}/folder/junk">
            	<i class="fa fa-filter"></i> Junk <span id="junkSpan" class="label label-warning pull-right"></span>
            </a>
         </li>
         <li class="{{ $folder=='trash' ? 'active' : '' }}">
         	<a href="{{ route('mails.index') }}/folder/trash">
            	<i class="fa fa-trash-o"></i> Trash <span id="trashSpan" class="label label-danger pull-right"></span>
            </a>
         </li>
      </ul>