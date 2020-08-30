<?php

namespace Boonei\Scaffold\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{

    public function __construct()
    {
        $this->middleware(['web', 'auth']);
    }

}
