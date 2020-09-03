<?php

namespace xqus\Clerk\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SubscriptionController extends Controller
{

    public function __construct()
    {
        $this->middleware(['web', 'auth']);
    }

    /**
     * Display the subscription page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        $user = Auth::user();

        return view('clerk::profile',[
            'user'          => $user
        ]);
    }

    /**
     * Create and get the setup intent.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function intent(Request $request){
        return response()->json($request->user()->createSetupIntent());
    }

    /**
     * Updates subscription for the user for a given product.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $product = $request->get('product');
        $plan = $request->get('plan');

        $paymentMethod = $user->defaultPaymentMethod()->id;

        if (! $user->subscribed($product)) {
            $user->newSubscription($product, $plan)
                 ->create($paymentMethod);
        }
        else {
            $user->subscription($product)->swap($plan);
        }

        return response()->json([
            'message' => 'Subscription updated.'
        ]);
    }

    /**
     * List available products and their plans.
     *
     * @return JsonResponse
     */
    public function getPlans()
    {
        return response()->json(config('scaffold.products'));
    }

}
