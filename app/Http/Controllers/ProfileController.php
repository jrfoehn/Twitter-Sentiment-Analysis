<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(){
    	$user = Auth::user();
    	return view('profile.index',compact('user'));
    }

    public function edit(Request $request)
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
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
            'log_id' => '',
            ]);

        $user = Auth::user();
        $user->update($request->toArray());
        $user->save();

        return redirect()->route('profile.index')->with('message','Your account has been updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();
        $user->delete();
        return redirect()->route('home')->with('message','Your account has been deleted successfully');   
    }


}

