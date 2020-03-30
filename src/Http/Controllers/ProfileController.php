<?php

namespace Boonei\Scaffold\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controller;

class ProfileController extends Controller {

  public function __construct() {
        //$this->middleware('auth');
  }

  public function index() {
    return view('scaffold::profile',[
      'user'  => Auth::user(),
    ]);
  }

  public function save(Request $request) {
    $user = Auth::user();

    $this->validate(request(), [
      'name' => 'required',
      'email' => [
        'required',
        'email',
        Rule::unique('users')->ignore($user->id),
      ],
    ]);

      $user->name = request('name');
      $user->email = request('email');

      $user->save();

      return back()->with('status', 'Profile updated!');
  }

  public function savePassword(Request $request) {
    $user = Auth::user();

    $this->validate(request(), [
      'password' => 'required|min:8|confirmed'
    ]);

      $user->password = Hash::make(request('password'));

      $user->save();

      return back()->with('status', 'Password changed!');
  }
}
