    @extends('layouts.crud')

    @section('innercontent')

    <?php   $fields = [
    'firstname' => 'First name',
    'name' => 'Name',
    'username' => 'Username',
    'email' => 'Email',
    'address' => 'Address',
    'postcode' => 'Postal code',
    'town' => 'Town']; ?>
    
    <div class="panel-heading">Edit your profile</div>

    <div class="panel-body">                
        
        {!! Form::model($user,['route'=>['profile.update', $user->id],'class'=>'form-horizontal']) !!}
        {{ method_field('PATCH') }}

        @foreach($fields as $attribute => $label)

        <div class="form-group">
            
            {!! Form::label($attribute, $label, ['class' => 'col-md-4 control-label']) !!}
            
            <div class="col-md-6">
                {!! Form::text($attribute, null, ['class'=>'form-control']) !!}
            </div>
            
        </div>
        @endforeach

        <div class="form-group">

            {!! Form::label('country','Country', ['class' => 'col-md-4 control-label']) !!}
            <div class="col-md-6">
                {!! Form::select('country', [],null, ['class'=>'form-control select-country']) !!}
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
                    {{ link_to_route('profile.index','Cancel',null,['class' => 'btn btn-default']) }}
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