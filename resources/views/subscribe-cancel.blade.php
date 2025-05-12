<!DOCTYPE html>
<html>
<head>
  <title>Subscribe with Stripe 3DS</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="max-w-md mx-auto p-6 bg-red-100 rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Subscription Cancelled</h2>
        <p>Your subscription was not completed. If this was a mistake, please try again.</p>
        <a href="{{ route('subscribe.show') }}" class="mt-4 inline-block text-blue-600 hover:underline">Start Subscription</a>
      </div>
</body>
</html>
