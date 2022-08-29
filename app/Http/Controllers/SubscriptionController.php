<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use App\Models\User;
Use App\Models\Product;
use Stripe;
use Session;
use Exception;

class SubscriptionController extends Controller
{
    public function index()
    {
        return view('subscription.create');
    }


    public function orderPost(Request $request)
    {
        $user = auth()->user();
        $input = $request->all();
        //dd($input);
        $token =  $request->stripeToken;
        $paymentMethod = $request->paymentMethod;
        try {

        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        if (is_null($user->stripe_id)) {
        $stripeCustomer = $user->createAsStripeCustomer();
        }

        \Stripe\Customer::createSource(
        $user->stripe_id,
        ['source' => $token]
        );

        $user->newSubscription('test',$input['plane'])
        ->create($paymentMethod, [
        'email' => $user->email
        ]);

        return back()->with('success','Subscription is completed.');
        } catch (Exception $e) {
        return back()->with('success',$e->getMessage());
        }
    }

    public function purchase(Request $request){
        $d = json_decode($request['my_data']);
        //print_r($d->id);die;
        $id = $d->id;

        $pid = $request['product_id'];

        $price = Product::find($pid,['price']);
        $finalPrice = $price->price * 100;



        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $res = $stripe->paymentIntents->create([
            'amount' => $finalPrice,
            'currency' => 'inr',
            'payment_method_types' => ['card'],
        ]);


        $stripe->paymentIntents->confirm(
            $res->id,
            ['payment_method' => 'pm_card_visa']
        );

        echo $res->confirmation_method;


    }
}
