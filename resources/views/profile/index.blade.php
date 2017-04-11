@extends('layouts.crud')
@section('innercontent')

<?php   $fields = [
        'firstname' => 'First name',
        'name' => 'Name',
        'username' => 'Username',
        'email' => 'Email',
        'address' => 'Address',
        'postcode' => 'Postal code',
        'town' => 'Town',
        'country' => 'Country',
        'activity' => 'Activity',
        'created_at' => 'Created_at',
        'updated_at' => 'Updated_at'
        ]; ?>

<div class="panel-heading">My Profile</div>

<div class="panel-body">      


<div class="col-md-12">
  @if(Session::has('message'))
    <div class="alert alert-success">{{ Session::get('message')}}</div>
  @endif     

<ul class="list-group" >
    @foreach($fields as $attribute => $label)
  <div class="col-md-6">
    <li class='list-group-item'>
    <h4><small> {{ $label }} :</small> {{ $user->$attribute }}</h4></li>
    </div>
    @endforeach
</ul>
 </div>
 <div class="col-md-12">
 <hr>
 <ul class="pager">
 {{ link_to_route('profile.edit','Edit your account',$user->id,['class' => 'btn btn-primary']) }}
 </ul>
 </div>
</div>


@endsection