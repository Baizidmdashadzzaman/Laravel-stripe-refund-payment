<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    SubscriptionController,RefundPaymentController
};

Route::get('/', function () {
    return view('welcome');
});


// Route::get('/subscribe', [SubscriptionController::class, 'showForm']);
// Route::post('/subscribe', [SubscriptionController::class, 'processSubscription']);

Route::get('/payments', [RefundPaymentController::class, 'index'])->name('payments.index');
Route::post('/payments/refund/{chargeId}', [RefundPaymentController::class, 'refund'])->name('payments.refund');
Route::post('/payments/refund-by-payment/{paymentId}', [RefundPaymentController::class, 'refund_via_paymentid'])->name('payments.refund_by_payment');


Route::get('/subscribe', [SubscriptionController::class, 'show'])->name('subscribe.show');
Route::post('/subscribe', [SubscriptionController::class, 'create'])->name('subscribe.create');
Route::get('/subscribe/success', [SubscriptionController::class, 'success'])->name('subscribe.success');
Route::get('/subscribe/cancel', [SubscriptionController::class, 'cancel'])->name('subscribe.cancel');

// Webhook endpoint
Route::post('/stripe/webhook', [SubscriptionController::class, 'webhook'])->name('stripe.webhook');
