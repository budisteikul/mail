<?php
namespace budisteikul\mail\DataTables;

use budisteikul\mail\Models\Mail_Account;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;

class AccountsDataTable extends DataTable
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
			->addIndexColumn()
            ->addColumn('action', function ($account) {
				if($account->notify==1)
				{
					$label = ""	;
					$status = 0;
					$button = "btn-info";
					$icon = "fa-bell-o";
				}
				else
				{
					$label = "";
					$status = 1;
					$button = "btn-warning";
					$icon = "fa-bell-slash-o";
				}
				return '<div class="btn-group"><button id="btn-edit" type="button" onClick="EDIT(\''. $account->id .'\')" class="btn btn-success btn-flat btn-sm"><span class="fa fa-pencil"></span> Edit </button><button id="btn-del" type="button" onClick="DELETE(\''. $account->id .'\')" class="btn btn-danger btn-flat btn-sm"><span class="fa fa-trash-o"></span> Delete</button><button id="btn-status" type="button" onClick="UPDATE_NOTIF(\''. $account->id .'\',\''. $status .'\')" class="btn '.$button.' btn-flat btn-sm"><span class="fa '. $icon .'"></span> '.$label.' </button></div>';
            })
			->rawColumns(['action']);
    }
    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Mail_Account $model)
    {
        $query = Mail_Account::where('user_id', Auth::user()->id)->orderBy('created_at','desc')->get();
        return $query;
    }
    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->addAction(['title' => '','width' => '240px','class' => 'text-center'])
                    ->parameters([
						'dom'		   => '
						<"row col-sm-12"
						  <"col-sm-6"l><"col-sm-6"f>
						>

						<"row"
						  <"col-sm-12"
						      <"table-responsive mailbox-messages hideScroll"
							     tr
						      >
						  >
						>

						<"row col-sm-12"
						  <"col-sm-6"i><"col-sm-6"p>
						>'
                        ,
						'order'	=> [],
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
            ["name" => "DT_RowIndex", "title" => "No", "data" => "DT_RowIndex", "orderable" => false, "render" => null,'searchable' => false, 'width' => '20px', 'class' => 'text-center'],
			["name" => "name", "title" => "Name", "data" => "name"],
			["name" => "email", "title" => "Email", "data" => "email"]
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