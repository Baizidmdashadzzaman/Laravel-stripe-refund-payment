<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;
use Stripe\SetupIntent;

class SubscriptionController extends Controller
{
    public function showForm()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $intent = SetupIntent::create();

        return view('subscribe', [
            'clientSecret' => $intent->client_secret,
            'stripeKey' => env('STRIPE_KEY')
        ]);
    }

    public function processForm(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $customer = Customer::create([
            'email' => $request->email,
            'payment_method' => $request->payment_method,
            'invoice_settings' => [
                'default_payment_method' => $request->payment_method,
            ],
        ]);


        $subscription = Subscription::create([
            'customer' => $customer->id,
            'items' => [
                ['price' => env('STRIPE_PRICE_ID')],
            ],
        ]);

        return redirect()->route('subscribe.form')->with('success', 'Subscription successful!');
    }
}
