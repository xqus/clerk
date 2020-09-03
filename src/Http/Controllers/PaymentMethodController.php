<?php

namespace xqus\Clerk\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{

    public function __construct()
    {
        $this->middleware(['web', 'auth']);
    }

    /**
     * Add a payment method and remove old payment methods.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request)
    {
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

        return response()->json(['message' => 'Payment method added.'], 201);
    }

    /**
     * Returns the payment methods the user has saved.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function get(Request $request)
    {
        $user = $request->user();

        $methods = array();

        if($user->hasPaymentMethod() ){
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

        return response()->json($methods);
    }

    /**
     * Removes a payment method for the current user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function remove(Request $request)
    {
        $user = $request->user();

        $paymentMethod = $request->get('id');

        $paymentMethods = $user->paymentMethods();

        foreach($paymentMethods as $method)
        {
            if($method->id == $paymentMethod)
            {
                $method->delete();
                break;
            }
        }

        return response()->json(['message' => 'Payment method removed.']);
    }

}
