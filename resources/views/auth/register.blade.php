@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Register</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">
                        {{ csrf_field() }}

                        <?php $fields = $fields = [
                        'firstname' => 'First name',
                        'name' => 'Name',
                        'username' => 'Username',
                        'email' => 'Email',
                        'address' => 'Address',
                        'postcode' => 'Postal code',
                        'town' => 'Town']; ?>

                        @foreach( $fields as $key => $value )

                        <div class="form-group{{ $errors->has($key) ? ' has-error' : '' }}">
                            <label for="{{ $key }}" class="col-md-4 control-label">{{$value}}</label>

                            <div class="col-md-6">
                                <input id="{{ $key }}" type="text" class="form-control" name="{{ $key }}" value='{{ old($key) }}' required autofocus>

                                @if ($errors->has($key))
                                    <span class="help-block">
                                        <strong>{{ $errors->first($key) }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        @endforeach

                         <div class="form-group{{ $errors->has('country') ? ' has-error' : '' }}">
                            <label for="country" class="col-md-4 control-label">Country</label>

                            <div class="col-md-6">
                                    <select id='country' name='country' class='form-control select-country'>                                     
                                    </select>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('activity') ? ' has-error' : '' }}">
                            <label for="activity" class="col-md-4 control-label">Why do you inted to use this tool?</label>

                            <div class="col-md-6">
                                    <select id='activity' name='activity' class='form-control'>
                                    <option value="curiosity">Out of curiosity</option>
                                    <option value="professional">Professional researches</option>
                                    <option value="study">Scientifical researches</option>
                                    <option value="educational">Educational</option>
                                    <option value="other">Other reason</option>
                                    </select>

                                @if ($errors->has('activity'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('activity') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
  

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Register
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
