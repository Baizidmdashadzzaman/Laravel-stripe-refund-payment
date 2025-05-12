<?php
namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Refund;
use Stripe\PaymentIntent;

use Illuminate\Http\Request;

class RefundPaymentController extends Controller
{
    public function index()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $allCharges = [];
        $hasMore = true;
        $startingAfter = null;

        while ($hasMore) {
            $params = ['limit' => 100];
            if ($startingAfter) {
                $params['starting_after'] = $startingAfter;
            }
            $charges = Charge::all($params);
            //dd($charges);
            $allCharges = array_merge($allCharges, $charges->data);
            $hasMore = $charges->has_more;

            if ($hasMore) {
                $startingAfter = end($charges->data)->id;
            }
        }
        return view('refund.payment-list', ['payments' => $allCharges]);
    }

    public function refund($chargeId)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        try {
            $charge = Charge::retrieve($chargeId);

            if ($charge->refunded) {
                return back()->with('error', 'This charge has already been refunded.');
            }

            Refund::create([
                'charge' => $chargeId,
                // 'amount' => 5000, // Optional for partial refund
            ]);
            return back()->with('success', 'Refund successful.');
        } catch (\Exception $e) {
            return back()->with('error', 'Refund failed: ' . $e->getMessage());
        }
    }


    public function refund_via_paymentid($paymentId)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentId);

            if (empty($paymentIntent->latest_charge)) {
                return back()->with('error', 'No charge found in this PaymentIntent.');
            }

            $chargeId = $paymentIntent->latest_charge;

            Refund::create([
                'charge' => $chargeId,
                // 'amount' => 5000, // optional: for partial refund
            ]);
            return back()->with('success', 'Refund successful via PaymentIntent ID.');
        } catch (\Exception $e) {
            return back()->with('error', 'Refund failed: ' . $e->getMessage());
        }
    }




}
