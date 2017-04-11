@extends('layouts.crud')

@section('innercontent')
                <div class="panel-heading">Edit User</div>

                <div class="panel-body">
                        
                        <?php  $fields = [
                            'firstname' => 'First name',
                            'name' => 'Name',
                            'username' => 'Username',
                            'email' => 'Email',
                            'address' => 'Address',
                            'postcode' => 'Postal code',
                            'town' => 'Town'];?>
                    
                        {!! Form::model($user,['route'=>['admin.update', $user->id],'class'=>'form-horizontal']) !!}
                        {{ method_field('PATCH') }}

                         @foreach($fields as $attribute => $label)
                        <div class="form-group">
                        {!! Form::label($attribute, $label, ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-6" >
                        {!! Form::text($attribute, null, ['class'=>'form-control']) !!}
                        </div>

                        </div>
                        @endforeach

                        <div class="form-group">
                        {!! Form::label('country','Country', ['class' => 'col-md-4 control-label']) !!}
                        <div class='col-md-6'>
                        {!! Form::select('country', [],null, ['class'=>'form-control select-country']) !!}
                        </div>
                        </div>

                        <div class="form-group">
                        {!! Form::label('admin','Give admin rights to this user?', ['class' => 'col-md-4 control-label']) !!} <br>
                        <div class="col-md-6">
                        No {!! Form::radio('admin', '0') !!}
                        Yes {!! Form::radio('admin', '1') !!}
                        </div>
                        </div>

                         <div class="form-group">

                        {!! Form::label('activity', 'Intended Utilisation', ['class'=>'col-md-4 control-label']) !!}
                         <div class="col-md-6">
                        {!! Form::select('activity', [
                        'curiosity' => 'Out of curiosity',
                        'professional' => 'Professional researches',
                        'scientifical' => 'Scientifical researches',
                        'educational' => 'Educational',
                        'other' => 'Other reason'],
                        null, ['class'=>'form-control col-md-4']  ) !!} 
                        </div>
                        </div>
                        <hr>

                        <div class="col-md-12"> 
                            <ul class="pager">

                        {!! Form::button('Update',['type'=>'submit', 'class'=>'btn btn-primary']) !!}
                        {{ link_to_route('admin.index','Cancel',null,['class' => 'btn']) }}
                        {!! Form::close() !!}
                            </ul>
                        </div>

                </div>


                @if($errors)
                    @foreach($errors->all() as $error)
                        <ul class="alert alert-danger" >
                             <li> {{ $error }} </li>
                        </ul>
                    @endforeach
                @endif
@endsection