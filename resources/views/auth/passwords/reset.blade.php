@extends('voyager::auth.master')

@section('content')
    <div class="login-container">

        <p>{{ __('Remplissez ce formulaire pour créer un nouveau mot de passe') }}</p>

        <form action="{{ route('users.resetPwd') }}" method="POST">
            {{ csrf_field() }}
            <div class="form-group form-group-default" id="emailGroup">
                <label>{{ __('voyager::generic.email') }}</label>
                <div class="controls">
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="{{ __('voyager::generic.email') }}" class="form-control" required>
                </div>
            </div>
            <div class="form-group form-group-default" id="passwordGroup">
                <label>{{ __('Choisissez un mot de passe') }}</label>
                <div class="controls">
                    <input type="password" name="password" id="password" value="{{ old('password') }}" placeholder="{{ __('voyager::generic.password') }}" class="form-control validate @error('password') is-invalid @enderror" required>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-group form-group-default" id="passwordConfirmGroup">
                <label>{{ __('Confirmez le mot de passe') }}</label>
                <div class="controls">
                    <input type="password" name="password_confirmation" id="password_confirmation" value="{{ old('password_confirmation') }}" placeholder="{{ __('Entrez le même mot de passe') }}" class="form-control validate @error('password_confirmation') is-invalid @enderror" required>
                    @error('password_confirmation')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ _('les deux mots de passe doivent etre identique') }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            {{-- <input type="hidden" name="token" value="{{ $token }}"> --}}

            <div style="clear:both"></div>
            <div class="form-group">
                <button type="submit" class="btn btn-block login-button">
                    <span class="signingin hidden"><span class="voyager-refresh"></span> {{ __('Réinitialisation en cours') }}...</span>
                    <span class="signin">{{ __('Réinitialiser') }}</span>
                </button>
            </div>

            <div class="form-group">
                <br>
                <br>
                <br>
                <br>
                <div class="controls">
                    {{-- <a href="{{ route('create-account') }}" class="forgot-password">{{__("Créer un compte Yatou")}}</a> --}}
                    {{-- <a href="{{ route('voyager.login') }}" class="forgot-password pull-right">{{__("Connexion")}}</a> --}}
                </div>
            </div>

        </form>

    </div> <!-- .login-container -->
@endsection

@section('post_js')

    <script>
        var btn = document.querySelector('button[type="submit"]');
        var form = document.forms[0];
        var email = document.querySelector('[name="email"]');
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

    </script>
@endsection
