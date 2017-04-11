<?php

namespace App\Http\Controllers;
use App\User;
use Collective\Html\FormFacade;
use Collective\Support\Facades\Html;
use Collective\Support\Facades\Form;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $users = User::all();
        return view('admin.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function create(){
        return view('admin.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function store(Request $request){

        $this->validate($request,[
            'firstname' => 'required|max:50',
            'name' => 'required|max:50',
            'username' => 'required|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'address'=>'required|max:255',
            'postcode' => 'required|min:5|max:6',
            'town' => 'required|max:255',
            'country' => 'required',
            'activity' => 'required',
            'admin' => 'required',
            'log_id' => '',
        ]);

        $request['password'] = bcrypt($request['password']);
        $request['log_id'] = str_random(64);

        User::create($request->toArray());
        return redirect()->route('admin.index')->with('message','User has been added successfully');
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

    public function edit(Request $request, $user_id)
    {
        $user = User::find($user_id);
        return view('admin.edit', compact('user','fields'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $user_id)
    {
        $this->validate($request,[
            'firstname' => 'required|max:50',
            'name' => 'required|max:50',
            'username' => 'required|max:50',
            'email' => 'required|email|max:50',
            'address'=>'required|max:100',
            'postcode' => 'required|min:5|max:6',
            'town' => 'required|max:50',
            'country' => 'required',
            'activity' => 'required',
            'admin' => 'required',
            'log_id' => '',
        ]);

        $user = User::find($user_id);
        $user->update($request->toArray());
        $user->save();

        return redirect()->route('admin.index')->with('message','User has been updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $user_id)
    {
        $user = User::find($user_id);
        $user->delete();
        return redirect()->route('admin.index')->with('message','User has been deleted successfully');   
    }
}
