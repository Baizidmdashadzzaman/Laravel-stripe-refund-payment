<!DOCTYPE html>
<html>
<head>
    <title>Stripe Subscription</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>

@if(session('success'))
    <h3>{{ session('success') }}</h3>
@endif

<form id="subscription-form" method="POST" action="{{ route('subscribe.process') }}">
    @csrf
    <label>Email:</label>
    <input type="email" name="email" required><br><br>

    <div id="card-element"></div>
    <input type="hidden" name="payment_method" id="payment_method">
    <br>
    <button id="submit-button" type="submit">Subscribe</button>
</form>

<script>
    const stripe = Stripe("{{ $stripeKey }}");
    const elements = stripe.elements();
    const cardElement = elements.create('card');
    cardElement.mount('#card-element');

    const form = document.getElementById('subscription-form');
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const { setupIntent, error } = await stripe.confirmCardSetup(
            "{{ $clientSecret }}",
            {
                payment_method: {
                    card: cardElement,
                    billing_details: {
                        email: form.email.value
                    }
                }
            }
        );

        if (error) {
            alert(error.message);
        } else {
            document.getElementById('payment_method').value = setupIntent.payment_method;
            form.submit();
        }
    });
</script>

</body>
</html>
