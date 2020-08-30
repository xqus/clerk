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

        return view('scaffold::profile',[
            'user'          => $user
        ]);
    }


    /**
     * API STUFF
     */


    public function getSetupIntent(Request $request){
        return $request->user()->createSetupIntent();
    }

    public function getSubscriptionPlans(Request $request){
        return config('scaffold.products');
    }

    public function postPaymentMethods(Request $request){
        $user = $request->user();
        $paymentMethodID = $request->get('payment_method');

        if($user->stripe_id == null) {
            $user->createAsStripeCustomer();
        }

        $user->addPaymentMethod($paymentMethodID);
        $user->updateDefaultPaymentMethod($paymentMethodID);

        /*
         * Delete all but default payment method.
         */
        $paymentMethods = $user->paymentMethods();

        foreach($paymentMethods as $method){
            if($method->id !== $user->defaultPaymentMethod()->id) {
                $method->delete();
            }
        }

        return response()->json(null, 204);
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
        $paymentID = $user->defaultPaymentMethod()->id;

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
