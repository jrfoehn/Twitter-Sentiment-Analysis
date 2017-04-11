   @extends('layouts.app')
   @section('content')

   <div class="container-fluid">
       <div class="row">
        <div class="col-md-12 col-md-offset-0">
            <div class="panel panel-default">

                <div class="panel-heading">Users</div>

                <?php $fields = [
                            'id' => '#',
                            'firstname' => 'First name',
                            'name' => 'Name',
                            'username' => 'Username',
                            'email' => 'Email',
                            'address' => 'Address',
                            'postcode' => 'Postal code',
                            'town' => 'Town',
                            'country' => 'Country',
                            'activity' => 'Activity',
                            'admin' => 'Admin Rights',
                            'created_at' => 'Created_at',
                            'updated_at' => 'Updated_at'
                            ]; ?>

                <!-- Eventual report messages -->
                @if(Session::has('message'))
                <div class="alert alert-success">{{ Session::get('message')}}</div>
                @endif

                We have {{ $users->count()}} users

                <div class="panel-body">
                    <table id='user_table' class='table table-striped table-hover'>
                        <thead>
                            <tr>
                                @foreach($fields as $attribute=>$label)

                                <th> {{ $label }} </th>

                                @endforeach
                                <td></td> <!-- Empty case corresponding  to the case given to 'Delete' and 'Update' buttons-->

                            </tr>
                        </thead>

                        <tbody>
                            @foreach($users as $user) <!-- 1 line per user -->
                            <tr>
                                @foreach($fields as $attribute=>$label) <!-- 1 cell per property -->
                                <td>
                                    @if($attribute !== 'admin')
                                    {{ $user->$attribute }}
                                    @else
                                    @if ($user->admin === 1)  Yes
                                    @else No
                                    @endif
                                    @endif
                                </td>
                                @endforeach

                                <td>
                                    @include('modals.delete')

                                    

                                    {{ Form::open(['method' => 'DELETE', 'route' => ['admin.destroy', $user->id]]) }}     {{ link_to_route('admin.edit','Edit',$user->id,['class' => 'btn btn-primary']) }}
                                    {!! Form::button('Delete',['class'=>'btn btn-danger', 'type' => 'submit']) !!}
                                    {!! Form::close() !!}

                                </td>
                            </tr>    



                            @endforeach
                        </tbody>
                    </table>

                    <hr>
                    <div class="col-md-12">
                        <ul class="pager">

                            {{ link_to_route('admin.create','Add New User',null,['class' => 'btn btn-primary']) }}

                        </ul>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

@endsection

<!-- Modal -->



