<?php

namespace budisteikul\mail\DataTables;

use budisteikul\mail\Models\Mail_Email;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MailsDataTable extends DataTable
{
	
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)
				->addColumn('checkbox', function ($mail) {
					return '<input class="icheckbox" type="checkbox" value="'. $mail->id .'" id="checkbox_'. $mail->id .'" onClick="SET_CHECKBOX(\'checkbox_'. $mail->id .'\',\''. $mail->id .'\')">';
				})
				->addColumn('star', function ($mail) {
					$content = '<a href="#" onClick="return CHANGE(\''. $mail->id .'\',\'1\')"><i class="fa fa-star text-yellow"></i></a>';
					if($mail->read>0)
					{
						$content = '<a href="#" onClick="return CHANGE(\''. $mail->id .'\',\'0\')"><i class="fa fa-star-o text-yellow"></i></a>';
					}
					return $content;
				})
				->addColumn('sender', function ($mail) {
					$from = $mail->from;
					if($from=="") $from = $mail->sender;
					return '<a href="'. route('mails.show',[ $mail->id, '' ]) .'">'. $from .'</a>';
					
					
				})
				->editColumn('subject', function ($mail) {
					if($mail->read>0)
					{
						return '<a href="'. route('mails.show',[ $mail->id, '' ]) .'" class="text-muted" >'. $mail->subject .' - <span>'. Str::words(strip_tags($mail->body_plain),10) .'</span></a>';
					}
					else
					{
						return '<a href="'. route('mails.show',[ $mail->id, '' ]) .'" class="text-muted" ><b>'. $mail->subject .'</b> - <span>'. Str::words(strip_tags($mail->body_plain),10) .'</span></a>';
					}
					
				})
				->addColumn('attachment', function ($mail) {
					$content = '';
					if($mail->attachment_count>0)
					{
						$content = '<i class="fa fa-paperclip"></i>';
					}
					return $content;
				})
				->addColumn('timestamp', function ($mail) {
					return $mail->created_at->diffForHumans();
				})
				->rawColumns(['checkbox','star','sender','subject','attachment','timestamp']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Mail_email $model)
    {
        $query = Mail_Email::where('user_id', Auth::user()->id)->where('folder',$this->folder)->orderBy('created_at','desc');
        return $query;
    }


    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
   
	public function html()
    {
		
		$archive_button = "";
		$empty = "";
		if($this->folder!="archive")
		{
			$archive_button = '<li class="paginate_button"><a onClick="return ARCHIVE()"><i class="fa fa-archive"></i></a></li>';
		}
		if($this->folder=="trash" || $this->folder=="junk")
		{
			$empty = '<li class="paginate_button"><a onClick="return EMPTY()"><i class="fa fa-eraser"></i></a></li>';
		}
		return $this->builder()
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->parameters([
						'dom'		   => '
						<"row"
						
						<"col-md-12 col-xs-12
						"
							f
						>
						<"col-md-8 col-xs-8
						"
							<"toolbar">
						>
						<"col-md-4 col-xs-4"
							p
						>
						>
						
						<"table-responsive no-padding"
							tr
						>
						<"row"
						<"col-md-6 col-xs-6
						"
							i
						>
						<"col-md-6 col-xs-6"
							
						>
						>
						',
						'pagingType'=> 'simple',
						'preDrawCallback' => 'function() { 
						
							$("#dataTableBuilder").css("width","100%");
							$("#dataTableBuilder thead").css("display","none");
							$(".dataTables_processing").css("margin-top","10px");
							$(\'.toolbar\').empty().append(\'<ul class="pagination"><li class="paginate_button"><a onClick="return SELECTALL_CHECKBOX()"><i id="check_all" class="fa fa-square-o"></i></a></li>'. $archive_button .'<li class="paginate_button"><a onClick="return DELETE()"><i class="fa fa-trash"></i></a></li>'.$empty.'</ul>\');
							$("#dataTableBuilder_paginate").removeClass("dataTables_paginate paging_simple").addClass("pull-right");
							
		
								
							}',
						'drawCallback' => 'function() {
								
								
								
								
								RELOAD_CHECKBOX();
								
								$("input").iCheck({
      								checkboxClass: "icheckbox_square-blue",
    							});
								
								$(".icheckbox").on("ifClicked", function(event){
									SET_CHECKBOX($(this).attr("id"),$(this).attr("value"))
								});
								
		
								
								}',
						
						'language' => [
								'paginate' => [ 'next' => '<i class="fa fa-chevron-right"></i>', 'previous' => '<i class="fa fa-chevron-left"></i>']
								]
                    ])
					->ajax('/'.request()->path());
    }

    /**
     * Get columns.
     *
     * @return array
     */
	
    protected function getColumns()
    {
        return [
			["name" => "checkbox", 'title'=>'', "data" => "checkbox", 'orderable' => false],
			["name" => "star", 'title'=>'', "data" => "star", 'orderable' => false, ],
            ["name" => "sender", 'title'=>'',  "data" => "sender", 'orderable' => false, 'searchable' => true, ],
			["name" => "subject", 'title'=>'', "data" => "subject", 'orderable' => false, 'searchable' => true, ],
			["name" => "body_html", 'title'=>'', "data" => "body_html", 'orderable' => false, 'searchable' => true, 'visible' => false, ],
			["name" => "attachment",'title'=>'', "data" => "attachment", 'orderable' => false,],
			["name" => "timestamp", 'title'=>'', "data" => "timestamp", 'orderable' => false,]
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Mail/Mails_' . date('YmdHis');
    }
}
