<?php

namespace Boonei\Scaffold\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class ProfileController extends Controller {

    public function __construct() {
        $this->middleware(['web', 'auth']);
    }

    public function index() {
        $user = Auth::user();

        $stripeCustomer = $user->createOrGetStripeCustomer();

        $paymentMethod = $user->defaultPaymentMethod();

        return view('scaffold::profile',[
            'user'          => $user,
            'intent'        => $user->createSetupIntent(),
            'paymentMethod' => $paymentMethod,
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

        return back()->with('status', __('Contact information updated.'));
    }

    public function savePassword(Request $request) {
        $user = Auth::user();

        $this->validate(request(), [
            'old_password' => 'required|password',
            'password'     => 'required|min:8|confirmed'
        ]);

        $user->password = Hash::make(request('password'));
        $user->save();

        Auth::logoutOtherDevices(request('old_password'));

        return back()->with('status', __('Your password was changed.'));
    }

    public function addPaymentMethod(Request $request) {
        $user = Auth::user();
        $token = $request->input('stripeToken');

        $user->addPaymentMethod($token);
        $user->updateDefaultPaymentMethod($token);

        return back()->with('status', __('Your credit card was updated.'));
    }

    public function remPaymentMethod(Request $request) {
        $user = Auth::user();

        $user->deletePaymentMethods();

        return back()->with('status', __('Your credit card details was removed.'));
    }

    /*public function updateSubscription(Request $request) {
        $user = Auth::user();

        $this->validate(request(), [
            'product' => 'required',
            'plan' => 'required'
        ]);

        if($request->input('plan')=='none') {
            $user->subscription($request->input('product'))->cancel();
            return back()->with('status', __('Your subscription was paused.'));
        }

        if (!$user->subscribed($request->input('product'))) {
            $paymentMethod = $user->defaultPaymentMethod();

            $user->newSubscription($request->input('product'), $request->input('plan'))->create($paymentMethod->id);
        } else {
            $user->subscription($request->input('product'))->swap($request->input('plan'));
        }

        return back()->with('status', __('Your subscription was changed.'));
    }*/

    /**
     * API STUFF
     */


    public function getSetupIntent(Request $request){
        return $request->user()->createSetupIntent();
    }

    public function getSubscriptionPlans(Request $request){
        return config('scaffold.products');
    }

    public function postPaymentMethods( Request $request ){
        $user = $request->user();
        $paymentMethodID = $request->get('payment_method');

        if( $user->stripe_id == null ){
            $user->createAsStripeCustomer();
        }

        $user->addPaymentMethod( $paymentMethodID );
        $user->updateDefaultPaymentMethod( $paymentMethodID );

        return response()->json( null, 204 );
    }

    /**
     * Returns the payment methods the user has saved
     *
     * @param Request $request The request data from the user.
     */
    public function getPaymentMethods( Request $request ){
        $user = $request->user();

        $methods = array();

        if( $user->hasPaymentMethod() ){
            foreach( $user->paymentMethods() as $method ){
                array_push( $methods, [
                    'id' => $method->id,
                    'brand' => $method->card->brand,
                    'last_four' => $method->card->last4,
                    'exp_month' => $method->card->exp_month,
                    'exp_year' => $method->card->exp_year,
                ] );
            }
        }

        return response()->json( $methods );
    }

    /**
     * Removes a payment method for the current user.
     *
     * @param Request $request The request data from the user.
     */
    public function removePaymentMethod( Request $request ){
        $user = $request->user();
        $paymentMethodID = $request->get('id');

        $paymentMethods = $user->paymentMethods();

        foreach( $paymentMethods as $method ){
            if( $method->id == $paymentMethodID ){
                $method->delete();
                break;
            }
        }

        return response()->json( null, 204 );
    }

    /**
     * Updates a subscription for the user
     *
     * @param Request $request The request containing subscription update info.
     */
    public function updateSubscription( Request $request ){
        $user = $request->user();
        $productID = $request->get('product');
        $planID = $request->get('plan');
        $paymentID = $paymentMethod = $user->defaultPaymentMethod()->id;

        if(!$user->subscribed($productID) ){
            $user->newSubscription($productID, $planID )
                ->create( $paymentID );
        }else{
            $user->subscription($productID)->swap( $planID );
        }

        return response()->json([
            'subscription_updated' => true
        ]);
    }
}
