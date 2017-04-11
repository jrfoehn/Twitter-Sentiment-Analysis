@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                 @if(Session::has('message'))

                    <div class="alert alert-success">{{ Session::get('message')}}</div>

                @endif

                <div class="panel-body">
                    Welcome, {{ Auth::user()->username }} You are logged in!
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
