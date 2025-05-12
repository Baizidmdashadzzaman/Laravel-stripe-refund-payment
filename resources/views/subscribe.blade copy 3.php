<!DOCTYPE html>
<html>
<head>
  <title>Subscribe with Stripe 3DS</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <div class="max-w-md mx-auto p-6 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Subscribe Monthly</h2>
        <p class="mb-4">Enter your email to start a $10/month subscription.</p>
        <form method="POST" action="{{ route('subscribe.create') }}">
          @csrf
          <div class="mb-4">
            <label for="email" class="block mb-1">Email</label>
            <input type="email" name="email" id="email" required class="w-full p-2 border rounded">
          </div>
          <input type="hidden" name="price_id" value="{{ $priceId }}">
          <button type="submit" class="w-full py-2 px-4 bg-blue-600 text-white rounded hover:bg-blue-700">
            Proceed to Checkout
          </button>
        </form>
      </div>
</body>
</html>
