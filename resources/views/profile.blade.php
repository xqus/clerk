@extends('layouts.app')

@section('content')

<div class="container pt-2 ">
  <div class="row justify-content-center">
    <div class="col-10">
      <h3 class="font-weight-light pb-4">{{ __('Account settings') }}</h3>
    </div>
  </div>
  <div class="row justify-content-center">
  <div class="col-3 bg-light text-dark border-right">
    <div class="mt-2 mb-2 text-uppercase font-weight-light">{{ __('Profile') }}</div>
    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
      <a class="nav-link active" id="v-pills-contact-tab" data-toggle="pill" href="#v-pills-contact" role="tab" aria-controls="v-pills-contact" aria-selected="true">Contact information</a>
      <a class="nav-link" id="v-pills-password-tab" data-toggle="pill" href="#v-pills-password" role="tab" aria-controls="v-pills-password" aria-selected="false">Password</a>
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
                <li>{{ $error }}</li>
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
                                                <strong>{{ $message }}</strong>
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
                                                <strong>{{ $message }}</strong>
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
                            <strong>{{ $message }}</strong>
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
                            <strong>{{ $message }}</strong>
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
                    <button id="sbmtBtn" type="submit" class="btn btn-primary">
                        {{ __('Change password') }}
                    </button>
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
</div>

@endsection
