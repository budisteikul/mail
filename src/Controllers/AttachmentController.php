<?php

namespace budisteikul\mail\Controllers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use budisteikul\mail\Models\Mail_Attachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $attachment = Mail_Attachment::findOrFail($id);
        
        
        $path = storage_path('app/temp/'. Auth::user()->id .'/') . $id;
        Storage::makeDirectory(storage_path('app/temp/'. Auth::user()->id));

        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $client->request('GET', $attachment->file_url, [
            'sink' => $path
        ]);

        return response()->download($path, Str::ascii($attachment->file_name))->deleteFileAfterSend();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
