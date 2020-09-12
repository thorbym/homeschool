@extends('layouts.app')

@section('content')
<div class="container">

    <div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
        <h1 class="display-4">
            Pricing
        </h1>
        <p class="lead">
            Quickly build an effective pricing table for your potential customers with this Bootstrap example. It’s built with default Bootstrap components and utilities with little customization.
        </p>
    </div>

    <div class="card-deck mb-2 text-center">
        <div class="card mb-4 shadow-sm">
            <div class="card-header ">
                <h2 class="my-0 font-weight-normal">
                    Viewer
                </h2>
            </div>
            <div class="card-body">
                <h2 class="pricing-card-title">
                    £FREE <small class="text-muted">/ mo</small>
                </h2>
                <ul class="list-unstyled mt-3 mb-4">
                    <li>10 users included</li>
                    <li>2 GB of storage</li>
                    <li>Email support</li>
                    <li>Help center access</li>
                </ul>
                <a href="{{ route('register') }}" class="btn btn-lg btn-block btn-primary">Register for free</a>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header ">
                <h2 class="my-0 font-weight-normal">
                    Contributor
                </h2>
            </div>
            <div class="card-body">
                <h2 class="pricing-card-title">
                    £2 <small class="text-muted">/ mo</small>
                </h2>
                <ul class="list-unstyled mt-3 mb-4">
                    <li>20 users included</li>
                    <li>10 GB of storage</li>
                    <li>Priority email support</li>
                    <li>Help center access</li>
                </ul>
                <button type="button" id="contributorBasic" class="btn btn-lg btn-block btn-primary">Get started</button>
            </div>
        </div>
        
        <!--<div class="card mb-4 shadow-sm">
            <div class="card-header ">
                <h2 class="my-0 font-weight-normal">
                    Contributor<strong>+</strong>
                </h2>
            </div>
            <div class="card-body">
                <h2 class="pricing-card-title">
                    £5 <small class="text-muted">/ mo</small>
                </h2>
                <ul class="list-unstyled mt-3 mb-4">
                    <li>29 users included</li>
                    <li>15 GB of storage</li>
                    <li>Phone and email support</li>
                    <li>Help center access</li>
                </ul>
                <button type="button" id="contributorPlus" class="btn btn-lg btn-block btn-primary">Contact us</button>
          </div>
      </div>-->
      <div class="modal fade" id="loginOrRegisterModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
@endsection

<script>

    document.addEventListener('DOMContentLoaded', function() {

        var user_id = @json(Auth::check() ? Auth::user()->id : 0);

        $('#contributorBasic').on('click', function(){
            if (user_id) {
                window.location = '/checkout';
            } else {
                axios.get('/api/payments/loginOrRegister/basic')
                    .then(function (response) {
                        $('#loginOrRegisterModal').html(response.data).modal();
                    })
                    .catch(function (error) {
                        console.log(error);
                });
            }
        });

        $('#contributorPlus').on('click', function(){
            if (user_id) {
                window.location = '/checkout';
            } else {
                axios.get('/api/payments/loginOrRegister/plus')
                    .then(function (response) {
                        $('#loginOrRegisterModal').html(response.data).modal();
                    })
                    .catch(function (error) {
                        console.log(error);
                });
            }
        });

    });
</script>