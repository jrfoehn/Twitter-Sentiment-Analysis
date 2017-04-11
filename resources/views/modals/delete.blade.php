<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="modal-body">

        Delete this User?
        
      </div>
      <div class="modal-footer">

      {{ $user->id }}

            {{ Form::open(['method' => 'DELETE', 'route' => ['admin.destroy', $user->id]]) }}
            {!! Form::button('Delete',['class'=>'btn btn-danger', 'type' => 'submit']) !!}
            {!! Form::close() !!}

        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>