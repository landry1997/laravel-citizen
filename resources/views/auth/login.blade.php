@extends('voyager::auth.master')

@section('content')
@if ($message = \Session::get('error'))
    <div class="alert alert-danger alert-block">
        <strong>{{ $message }}</strong>
    </div>
@endif
{{-- @php
    dd(\Session::get('error'));
@endphp --}}
    <div class="login-container">

        <p>{{ __('voyager::login.signin_below') }}</p>

        <form action="{{ route('voyager.login') }}" method="POST">
            {{ csrf_field() }}
            <div class="form-group form-group-default" id="emailGroup">
                <label>{{ __('voyager::generic.email') }}</label>
                <div class="controls">
                    <input type="text" name="email" id="email" value="{{ old('email') }}" placeholder="{{ __('voyager::generic.email') }}" class="form-control" required>
                </div>
            </div>

            <div class="form-group form-group-default" id="passwordGroup">
                <label>{{ __('voyager::generic.password') }}</label>
                <div class="controls" style="display: flex;">
                    <input type="password" style="display: inline;" id="password" name="password" placeholder="{{ __('voyager::generic.password') }}" class="form-control validate @error('email') is-invalid @enderror" required>
                    <i class="fa-solid fa-eye" id="toggle_password" style="margin-top:-4px;"></i>
                </div>
            </div>
            <div class="form-group row mb-0" style="display: inline-block;">
                <div class="col-md-8 offset-md-4">
                    <input type="submit" class="btn btn-dark btn" style="display: inline-block; position: absolute;" value="{{ __('voyager::generic.login') }}">
                </div>
        </form>

        <div style="clear:both"></div>
        @if(!$errors->isEmpty())
            <div class="alert alert-red">
                <ul class="list-unstyled">
                    @foreach($errors->all() as $err)
                        <li>{{ __('Veuillez vérifier votre adresse mail ou votre mot de passe.') }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div> <!-- .login-container -->
@endsection

@section('post_js')
<script src="https://code.jquery.com/jquery-3.6.0.slim.min.js" integrity="sha256-u7e5khyithlIdTpu22PHhENmPcRdFiHRjhAuHcs05RI=" crossorigin="anonymous"></script>
<script src="https://kit.fontawesome.com/a669cdcd6c.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.3/moment.min.js" integrity="sha512-x/vqovXY/Q4b+rNjgiheBsA/vbWA3IVvsS8lkQSX1gQ4ggSJx38oI2vREZXpTzhAv6tNUaX81E7QBBzkpDQayA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $("#toggle_password").click(function() {
        var input = document.getElementById("password");
        console.log(input);
        if (input.type === "password"){
            input.type = "text";
            $("#toggle_password").removeClass("fa-solid fa-eye");
            $("#toggle_password").addClass("fa-solid fa-eye-slash");
        }
        else{
            input.type = "password";
            $("#toggle_password").removeClass("fa-solid fa-eye-slash");
            $("#toggle_password").addClass("fa-solid fa-eye");
        }
    });
</script>
    <script>
        var btn = document.querySelector('button[type="submit"]');
        var form = document.forms[0];
        var email = document.querySelector('[name="email"]');
        var password = document.querySelector('[name="password"]');
        btn.addEventListener('click', function(ev){
            if (form.checkValidity()) {
                btn.querySelector('.signingin').className = 'signingin';
                btn.querySelector('.signin').className = 'signin hidden';
            } else {
                ev.preventDefault();
            }
        });
        email.focus();
        document.getElementById('emailGroup').classList.add("focused");
        // Focus events for email and password fields
        email.addEventListener('focusin', function(e){
            document.getElementById('emailGroup').classList.add("focused");
        });
        email.addEventListener('focusout', function(e){
            document.getElementById('emailGroup').classList.remove("focused");
        });
        password.addEventListener('focusin', function(e){
            document.getElementById('passwordGroup').classList.add("focused");
        });
        password.addEventListener('focusout', function(e){
            document.getElementById('passwordGroup').classList.remove("focused");
        });
    </script>
@endsection
