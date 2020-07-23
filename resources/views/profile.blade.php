@extends('layouts.app')

@section('content')

<div class="container pt-2 ">
  <div class="row justify-content-center">
    <div class="col-10">
      <h3 class="font-weight-light pb-4">{{ __('Account settings') }}</h3>
    </div>
  </div>
  <div class="row justify-content-center">
  <div class="col-3 bg-light text-dark border-right rounded-left">
    <div class="mt-2 mb-2 text-uppercase font-weight-light">{{ __('Profile') }}</div>
    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
      <a class="nav-link active" id="v-pills-contact-tab" data-toggle="pill" href="#v-pills-contact" role="tab" aria-controls="v-pills-contact" aria-selected="true">{{ __('Contact information') }}</a>
      <a class="nav-link" id="v-pills-password-tab" data-toggle="pill" href="#v-pills-password" role="tab" aria-controls="v-pills-password" aria-selected="false">{{ __('Password') }}</a>
      <div class="dropdown-divider"></div>
      <div class="mt-2 mb-2 text-uppercase font-weight-light">{{ __('Payment') }}</div>
      <a class="nav-link" id="v-pills-card-tab" data-toggle="pill" href="#v-pills-card" role="tab" aria-controls="v-pills-card" aria-selected="true">{{ __('Credit card') }}</a>
      <a class="nav-link" id="v-pills-subscription-tab" data-toggle="pill" href="#v-pills-subscription" role="tab" aria-controls="v-pills-subscription" aria-selected="false">{{ __('Subscription') }}</a>
    </div>
  </div>
  <div class="col-7">
    <div class="tab-content" id="v-pills-tabContent">
      <div class="tab-pane fade show active" id="v-pills-contact" role="tabpanel" aria-labelledby="v-pills-contact-tab">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ __($error) }}</li>
            @endforeach
        </ul>
    </div>
@endif
                            <h5 class="mt-2 mb-2 font-weight-light">{{ __('Contact information') }}</h5>
                            <hr>
                            <form method="POST" action="{{ route('scaffold.profile.patch') }}">
                                @csrf
                                @method('PATCH')
                                <div class="form-group row">
                                    <label for="name" class="col-md-3 col-form-label text-md-right">{{ __('Name') }}</label>

                                    <div class="col-md-7">
                                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $user->name }}" required autocomplete="name" autofocus>

                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ __($message) }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="email" class="col-md-3 col-form-label text-md-right">{{ __('E-mail') }}</label>

                                    <div class="col-md-7">
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $user->email }}" required autocomplete="email">

                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ __($message) }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <div class="col-md-7 offset-md-3">
                                        <button id="sbmtBtn" type="submit" class="btn btn-primary">
                                            {{ __('Update') }}
                                        </button>
                                    </div>
                                </div>
                              </form>
      </div>
      <div class="tab-pane fade" id="v-pills-password" role="tabpanel" aria-labelledby="v-pills-password-tab">
        <h5 class="mt-2 mb-2 font-weight-light">{{ __('Password settings') }}</h5>
        <hr>
        <form method="POST" action="{{ route('scaffold.profile.password.patch') }}">
            @csrf
            @method('PATCH')
            <div class="form-group row">
                <label for="old_password" class="col-md-4 col-form-label text-md-right">{{ __('Current password') }}</label>

                <div class="col-md-6">
                    <input id="old_password" type="password" class="form-control @error('old_password') is-invalid @enderror" name="old_password" required autocomplete="old-password">

                    @error('old_password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ __($message) }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('New password') }}</label>

                <div class="col-md-6">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ __($message) }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm password') }}</label>

                <div class="col-md-6">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                </div>
            </div>

            <div class="form-group row mb-0">
                <div class="col-md-6 offset-md-4">
                    <button id="sbmtBtnPwd" type="submit" class="btn btn-primary">
                        {{ __('Change password') }}
                    </button>
                </div>
            </div>
        </form>
      </div>

      <div class="tab-pane fade" id="v-pills-card" role="tabpanel" aria-labelledby="v-pills-card-tab">
        @if($paymentMethod)
        <h5 class="mt-2 mb-2 font-weight-light">{{ __('My credit card') }}</h5>
        <hr>
        <h5><i class="fab fa-cc-{{$paymentMethod->card->brand}}"></i> **** **** **** {{$paymentMethod->card->last4}} <small>{{$paymentMethod->card->exp_month}} / {{$paymentMethod->card->exp_year}}</small></h5>

        <form class=pt-3"" action="{{ route('scaffold.profile.paymentmethod.delete') }}" method="post" id="add-card-form">
          @csrf
          @method('DELETE')
          <button class="btn btn-danger mt-2 mb-4">
            {{__('Delete my credit card')}}
          </button>
        </form>
        @endif

        <!-- Stripe Elements Placeholder -->
        <h5 class="mt-2 mb-2 font-weight-light">{{ __('Add / update credit card') }}</h5>
        <hr>
        <p><small>
        {{__("Want to update the credit card that we have on file? Provide the new details here. And don't worry; your card information will never directly touch our servers.")}}
        </small></p>
        <div id="card-element" class="bg-light mt-2 ml-4 mr-4"></div>


        <div class="form-group row pt-2">
            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Name on card') }}</label>

            <div class="col-md-6">
                <input id="card-holder-name" type="text">
            </div>
        </div>

        <button id="card-button" class="btn btn-success offset-md-4 mt-2"  data-secret="{{ $intent->client_secret }}">
          {{__('Update credit card')}}
        </button>

        <form action="{{ route('scaffold.profile.paymentmethod.post') }}" method="post" id="add-card-form">
          @csrf
        </form>

      </div>

      <div class="tab-pane fade" id="v-pills-subscription" role="tabpanel" aria-labelledby="v-pills-subscription-tab">
        <h5 class="mt-2 mb-2 font-weight-light">{{ __('My subscription') }}</h5>
        <hr>
        @foreach (config('scaffold.products') as $productId => $product)
          <h6 class="mt-2 mb-2 font-weight-light">{{ $product['title'] }}</h6>
          @if ($user->subscribed($productId) && $user->subscription($productId)->cancelled())
          <div class="alert alert-warning" role="alert">
          {{__('Your subscription is cancelled but will be active until your billing period ends.')}}
          </div>
          @endif
          <div class="row">
          @foreach($product['plans'] as $plan => $planData)
            <div class="{{$user->subscribedToPlan($plan, $productId) ? "bg-success text-light " : "" }}mr-2 ml-2 mb-2 card col-md-3" id="card_{{ $productId }}_{{ $plan }}">
              <div class="card-body text-center">
                {{ $planData['price'] }}
                <small>
                  {{ $planData['interval'] }}<br>
                  {{$user->subscribedToPlan($plan, $productId) ? "(current plan)" : "" }}
                </small>
              </div>
            </div>
          @endforeach
          @if($user->subscribed($productId) && !$user->subscription($productId)->cancelled())
          <div class="mr-2 ml-2 mb-2 card col-md-3" id="card_{{ $productId }}_none">
            <div class="card-body text-center">
              {{ __('Pause') }}
              <small>
                {{ __('my subscription') }}
              </small>
            </div>
          </div>
          </div>
          @endif
          @if($user->hasPaymentMethod())
          <form action="{{ route('scaffold.profile.subscription.patch') }}" method="post">
            @csrf
            @method('PATCH')
            <input type="hidden" id="field_{{ $productId }}_product" name="product" value="{{ $productId }}" method="POST">
            <input type="hidden" id="field_{{ $productId }}_plan" name="plan" method="POST">
            <hr>
            <button id="sbmtBtnSub" type="submit" class="btn btn-primary">
                {{ __('Update subscription') }}
            </button>
          </form>
          @else
          <div class="alert alert-info" role="alert">
            {{__("You don't have any credit cards added to your profile. You should do that first.")}}
          </div>
          @endif
          <hr>
        @endforeach
      </div>
    </div>
  </div>
</div>
</div>


<script>
$(document).ready(function(){
@foreach (config('scaffold.products') as $productId => $product)
  $("[id^=card_{{ $productId }}]").on( "click", function() {
      $('#field_{{ $productId }}_plan').val('');
      $("[id^=card_{{ $productId }}]").removeClass( "bg-primary text-white" );
  });

  $("[id^=card_{{ $productId }}_none]").on( "click", function() {
    $('#field_{{ $productId }}_plan').val('none');
    $('#card_{{ $productId }}_none').addClass( "bg-primary text-white" );
  });

  @foreach($product['plans'] as $plan => $planData)
    $("[id^=card_{{ $productId }}_{{$plan}}]").on( "click", function() {
      $('#field_{{ $productId }}_plan').val('{{$plan}}');
      $('#card_{{ $productId }}_{{$plan}}').addClass( "bg-primary text-white" );
    });
  @endforeach

@endforeach
});
</script>

<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{env('STRIPE_KEY')}}');

    var style = {
  base: {
    color: "#000000",
    iconColor: "#333333",
    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
    fontSmoothing: "antialiased",
    fontSize: "20px",
    "::placeholder": {
      color: "#333333",
    },
  },
  invalid: {
    color: "#fa755a",
    iconColor: "#fa755a",
  },
};

    const elements = stripe.elements();
    const cardElement = elements.create('card', { style: style });

    cardElement.mount('#card-element');

    const cardHolderName = document.getElementById('card-holder-name');
    const cardButton = document.getElementById('card-button');

    cardButton.addEventListener('click', async (e) => {
        const { paymentMethod, error } = await stripe.createPaymentMethod(
            'card', cardElement, {
                billing_details: { name: cardHolderName.value }
            }
        );

        if (error) {
            // Display "error.message" to the user...
        } else {
            stripeTokenHandler(paymentMethod.id);
        }
    });


 function stripeTokenHandler(token) {
     // Insert the token ID into the form so it gets submitted to the server
     var form = document.getElementById('add-card-form');
     var hiddenInput = document.createElement('input');
     hiddenInput.setAttribute('type', 'hidden');
     hiddenInput.setAttribute('name', 'stripeToken');
     hiddenInput.setAttribute('value', token);
     form.appendChild(hiddenInput);

     // Submit the form
     form.submit();
   }
</script>
@endsection
