<?php
namespace budisteikul\mail\Controllers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use budisteikul\mail\DataTables\AccountsDataTable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use budisteikul\mail\Models\Mail_Account;
use budisteikul\mail\Helpers\MailHelper;

class SettingController extends Controller
{
	
	public function __construct()
	{
    	$this->middleware(['auth', 'verified']);
	}
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AccountsDataTable $dataTable)
    {
		$stdClass = app();
    	$settings = $stdClass->make('stdClass');
		$settings->pushover_user = MailHelper::get_option('pushover_user');
		$settings->pushover_app = MailHelper::get_option('pushover_app');
		
        return $dataTable->render('mail::mails.settings.index', compact('settings'));
		
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('mail::mails.settings.create');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		
		if($request->input('option')=='option')
		{
			
			MailHelper::set_option('pushover_app',$request->input('pushover_app'));
			MailHelper::set_option('pushover_user',$request->input('pushover_user'));
			
			return response()->json([
				'id' => '1',
				'message' => 'Update notification success'
			]);
		}
		
		
        $validator = Validator::make($request->all(), [
          	'name' => 'required|max:190',
			'email' => 'required|email|max:190',
       	]);
        
       	if ($validator->fails()) {
            $errors = $validator->errors();
			return response()->json($errors);
       	}
	   
	   
		$name = $request->input('name');
		$email = $request->input('email');
		
		$email_check = Mail_Account::where('email',$email)->get();
		if(@count($email_check)) return response()->json(['email' => 'The email has already taken']);
		
		$account = new Mail_Account;
		$account->user_id = Auth::user()->id;
        $account->name = $name;
		$account->email = $email;
        $account->save();
		
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $result = Mail_Account::findOrFail($id);
		return view('mail::mails.settings.edit')->with('result',$result);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
		
		if($request->input('option')=='option')
		{
			
			$status = $request->input('status');
			$account = Mail_Account::findOrFail($id);
			$account->notify = $status;
			$account->save();
			
			return response()->json([
				'id' => '1',
				'message' => 'Update notification success'
			]);
		}
		
        $validator = Validator::make($request->all(), [
          	'name' => 'required|max:190',
			'email' => 'required|email|max:190',
       	]);
        
       	if ($validator->fails()) {
            $errors = $validator->errors();
			return response()->json($errors);
       	}
		
		$name = $request->input('name');
		$email = $request->input('email');
		
		$account = Mail_Account::findOrFail($id);
		
		if($email!=$account->email)
		{
			$email_check = Mail_Account::where('email',$email)->get();
			if(@count($email_check)) return response()->json(['email' => 'The email has already taken']);
		}
		
        $account->name = $name;
		$account->email = $email;
        $account->save();
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $accounts = Mail_Account::findOrFail($id);
     	$accounts->delete();
    }
}