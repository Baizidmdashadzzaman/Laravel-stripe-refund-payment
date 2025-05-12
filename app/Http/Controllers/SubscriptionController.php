<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class SubscriptionController extends Controller
{
    public function show()
    {
        $priceId = config('services.stripe.price_monthly');
        $publishable = config('services.stripe.key');
        return view('subscribe', compact('priceId', 'publishable'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'price_id' => 'required'
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        // Create Checkout Session
        $session = Session::create([
            'payment_method_types' => ['card'],
            'mode'                 => 'subscription',
            'customer_email'       => $request->email,
            'line_items'           => [[
                'price'    => $request->price_id,
                'quantity' => 1,
            ]],
            'success_url'          => route('subscribe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'           => route('subscribe.cancel'),
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        return view('subscribe-success', compact('sessionId'));
    }

    public function cancel()
    {
        return view('subscribe-cancel');
    }

    public function webhook(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        // Handle relevant event types
        switch ($event->type) {
            case 'checkout.session.completed':
                // Retrieve session: $session = $event->data->object;
                break;
            case 'invoice.paid':
                // Handle recurring payment
                break;
            case 'invoice.payment_failed':
                // Notify customer
                break;
        }

        return response('Webhook handled', 200);
    }
}
