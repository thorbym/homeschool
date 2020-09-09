@extends('layouts.app')

@section('content')
<style>
.StripeElement {
  box-sizing: border-box;

  height: 40px;

  padding: 10px 12px;

  border: 1px solid transparent;
  border-radius: 4px;
  background-color: white;

  box-shadow: 0 1px 3px 0 #e6ebf1;
  -webkit-transition: box-shadow 150ms ease;
  transition: box-shadow 150ms ease;
}

.StripeElement--focus {
  box-shadow: 0 1px 3px 0 #cfd7df;
}

.StripeElement--invalid {
  border-color: #fa755a;
}

.StripeElement--webkit-autofill {
  background-color: #fefde5 !important;
}
</style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">{{ __('Subscribe') }}</div>

                <div class="card-body">

                    <div class="form-group row">
                        <label for="package" class="col-md-4 col-form-label text-md-right">{{ __('Package') }}</label>
                        <div class="col-md-6">
                            <div id="package" class="form-control" style="border: 0">Contributor</div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="price" class="col-md-4 col-form-label text-md-right">{{ __('Price') }}</label>
                        <div class="col-md-6">
                            <div id="price" class="form-control" style="border: 0">£2 per calendar month</div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="card-element" class="col-md-4 col-form-label text-md-right">{{ __('Card Details') }}</label>
                        <div class="col-md-7">
                            <div id="card-element" class="form-control"></div>
                        </div>
                    </div>
                    <br />
                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button class="btn btn-primary" id="card-button" data-secret="{{ $intent->client_secret }}">
                                {{ __('Subscribe') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script src="https://js.stripe.com/v3/"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const user = @json(Auth::user());
        console.log(user);
        const stripe = Stripe('pk_test_51HH2f9DW1slEgvERDQeE7hrOA0F1JO5oVbjpGm93v1RESg5d2CNRWKKYVf6xfiP0g5ogW11tRPgodz6b9QpR2nhB00tAXpbH2W');

        const elements = stripe.elements();
        const cardElement = elements.create('card');

        cardElement.mount('#card-element');
        const cardButton = document.getElementById('card-button');
        const clientSecret = cardButton.dataset.secret;

        cardButton.addEventListener('click', async (e) => {
            const { setupIntent, error } = await stripe.confirmCardSetup(
                clientSecret, {
                    payment_method: {
                        card: cardElement,
                        billing_details: {
                            name: user.name,
                            email: user.email
                        }
                    }
                }
            );

            if (error) {
                console.log(error)
            } else {
                console.log(setupIntent)
                axios.post('/api/payments/paymentMethod', {
                    paymentMethod: setupIntent.payment_method
                })
                .then(function (response) {
                    console.log(response);
                    //window.location = '/calendar';
                })
                .catch(function (error) {
                    console.log(error);
                });
            }
        });
    });
</script>