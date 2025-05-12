{{-- resources/views/subscribe.blade.php --}}

<!DOCTYPE html>
<html>
<head>
  <title>Subscribe with Stripe 3DS</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
  <h1>Monthly Subscription</h1>

  <form id="subscription-form">
    <input type="email" id="email" placeholder="Email" required><br><br>
    <div id="card-element" style="max-width: 350px; padding: 10px; border: 1px solid #ccc;"></div><br>
    <button id="submit">Subscribe</button>
  </form>

  <p id="message" style="color: green;"></p>

  <script>
    const stripe = Stripe("{{ $stripeKey }}");
    const elements = stripe.elements();
    const card    = elements.create('card');
    card.mount('#card-element');

    const form = document.getElementById('subscription-form');
    const msg  = document.getElementById('message');

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      msg.textContent = '';

      // 1️⃣ Do SCA auth now via SetupIntent (3DS popup)
      const {setupIntent, error: setupError} = await stripe.confirmCardSetup(
        "{{ $clientSecret }}", {
          payment_method: {
            card: card,
            billing_details: { email: document.getElementById('email').value }
          }
        }
      );
      if (setupError) {
        return alert(setupError.message);
      }

      // 2️⃣ Send confirmed payment_method ID to backend
      const res = await fetch('/subscribe', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
          email: document.getElementById('email').value,
          payment_method: setupIntent.payment_method
        })
      });
      const data = await res.json();

      // 3️⃣ If first invoice needs another 3DS step (immediate charge)
      if (data.requiresAction) {
        const {error: confirmError} = await stripe.confirmCardPayment(
          data.paymentIntentSecret
        );
        if (confirmError) {
          return alert(confirmError.message);
        }
        msg.textContent = 'Subscription is now active!';
      } else {
        msg.textContent = data.message;
      }
    });
  </script>
</body>
</html>
