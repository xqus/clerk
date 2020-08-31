<?php

namespace Boonei\Scaffold\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfileController extends Controller {

    public function __construct() {
        $this->middleware(['web', 'auth']);
    }

    public function index() {
        $user = Auth::user();

        return view('scaffold::profile',[
            'user'          => $user
        ]);
    }

    public function getSetupIntent(Request $request){
        return $request->user()->createSetupIntent();
    }
}
