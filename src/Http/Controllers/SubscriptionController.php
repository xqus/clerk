<?php

namespace Boonei\Scaffold\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubscriptionController extends Controller
{

    public function __construct()
    {
        $this->middleware(['web', 'auth']);
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
