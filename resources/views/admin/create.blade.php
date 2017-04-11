@extends('layouts.crud')

@section('innercontent')

<div class="panel-heading"><h4>Create a new User</h4></div>

<div class="panel-body">                

<?php  $fields = [
        'firstname' => 'First name',
        'name' => 'Name',
        'username' => 'Username',
        'email' => 'Email',
        'address' => 'Address',
        'postcode' => 'Postal code',
        'town' => 'Town']; ?>

    {!! Form::open(['route'=>'admin.store']) !!}

    @foreach($fields as $attribute => $label)

    <div class="form-group">
        {!! Form::label($attribute, $label, ['class' => 'col-md-6 control-label']) !!}
        {!! Form::text($attribute, null, ['class'=>'form-control col-md-4']) !!}
    </div>

    @endforeach

    <div class="form-group">
        {!! Form::label('country','Country', ['class' => 'col-md-6 control-label']) !!}
        {!! Form::select('country', [], null, ['class'=>'form-control select-country']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('password','Password', ['class' => 'col-md-6 control-label']) !!}
        {!! Form::password('password', ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        <label for="password-confirm" class="col-md-6 control-label">Confirm Password</label>
        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
    </div>

    <div class="form-group">

        {!! Form::label('activity', 'Intended Utilisation', ['class'=>'col-md-6 control-label']) !!}
        {!! Form::select('activity', [
            'curiosity' => 'Out of curiosity',
            'professional' => 'Professional researches',
            'scientifical' => 'Scientifical researches',
            'educational' => 'Educational',
            'other' => 'Other reason'],
            null, ['class'=>'form-control col-md-4']  ) !!} 
        </div>

        <div class="form-group">
            {!! Form::label('admin','Give admin rights to this user?') !!}
            No {!! Form::radio('admin', '0', 'checked') !!}
            Yes {!! Form::radio('admin', '1') !!}
        </div>


        {!! Form::hidden('log_id', str_random(64) )!!}
        <hr>
        <div class="col-md-12">
            <ul class="pager">

                {!! Form::button('Create',['type'=>'submit', 'class'=>'btn btn-primary']) !!}
                {{ link_to_route('admin.index','Cancel',null,['class' => 'btn']) }}
                {!! Form::close() !!}
            </ul>
        </div>
        </div>




        @if($errors)
        @foreach($errors->all() as $error)
        <ul class="alert alert-danger">
           <li>   {{ $error }} </li>
       </ul>
       @endforeach
       @endif
       @endsection