
<link rel="stylesheet" href="{{ asset("/admin/plugins/datatables/dataTables.bootstrap.css") }}">
<div class="box">
  <div class="box-header">
    <h3 class="box-title"><b>User</b></h3>
    <button class="pull-right" id="create-user" style="background-color: #3c8dbc;color: white;width: 87px;    font-size: 19px;">Create</button>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
    <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-sm-12">
          <div class="c_alert alert hide" role='alert'>

          </div>
          <form id="frmuploads" name='frmuploads' method='post' enctype="multipart/form-data">
          <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
            <thead>
              <tr role="row">
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Upload</th>
                <th>Role</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
                  @foreach($users as $user)
                  <tr role="row" class="odd">
                    <td class="sorting_1">{{$user->first_name}}</td>
                    <td>{{$user->last_name}}</td>
                    <td>{{$user->email}}</td>
                    <td><input type="file" name='fileupload[{{$user->id}}]' onChange="func_upload(this.value, {{ $user->id }})" accept=".pdf" /></td>
                    <td>{{(count($user->roles) > 0 ? $user->roles[0]->display_name : '')}}</td>
                    <td>
                      @if($user->is_active)
                          <a href="javascript:void(0);" onclick="isActive({{$user->is_active}}, {{$user->id}});" class="user_{{$user->id}}">Active</a>
                      @else
                          <a href="javascript:void(0);" onclick="isActive({{$user->is_active}}, {{$user->id}});" class="user_{{$user->id}}">Inactive</a>
                      @endif
                      | <a href="javascript:void(0);" onclick="func_edit('{{$user->id }}')">Edit</a>
                    </td>
                  </tr>
                @endforeach
            </tbody>
          </table>
            <iframe src='' name='ifrmUploads' id='ifrmUploads' class="hide"></iframe>
            <input type="hidden" name='_token' value="{{ csrf_token() }}">
            <input type='hidden' name='event_value' value="" />
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- /.box-body -->
</div>

<script src="{{ asset('/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('/admin/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
<script>
  $(function () {
    $("#example1").DataTable();
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false
    });
  });
</script>
<script>
  function func_edit(id)
  {
    $('.content').html('');
    $.ajax({
        url: "<?php echo url('admin/userform'); ?>/"+id,
        type: "GET",
        success: function (data) {
            $('.content').html(data);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            //what to do in error
        },
        timeout: 15000//timeout of the ajax call
    });
  }
  $('#create-user').click(function (e) {
    e.preventDefault();
    $('.content').html('');
    $.ajax({
        url: "<?php echo url('admin/userform'); ?>",
        type: "GET",
        success: function (data) {
            $('.content').html(data);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            //what to do in error
        },
        timeout: 15000//timeout of the ajax call
    });
  });

  function func_upload(value, id)
  {
    document.forms.frmuploads.event_value.value = id;
    document.forms.frmuploads.action = "/site/manager_user_capability";
    document.forms.frmuploads.target = 'ifrmUploads';
    document.forms.frmuploads.submit();
  }

  function func_return_upload(type, message)
  {
    $(".c_alert").removeClass('hide').addClass(type);
    var html    = "<label class='control-label'>"+message+"</label>";

    $(".c_alert").html(html);
  }

  function isActive(activeLinkLabel, userID) {
      $.ajax({
          url: "/admin/users/isactive?activeLinkLabel="+activeLinkLabel+"&&userID="+userID,
          // type: 'post'
      }).done(function(data){
        var newActiveLabel = "";
        var newActiveDigit = "";
        if(activeLinkLabel == "1")
          newActiveLabel = "Inactive";
        else
          newActiveLabel = "Active";

        if(activeLinkLabel == "1")
          newActiveDigit = "0";
        else
          newActiveDigit = "1";

        $('.user_'+userID).html(newActiveLabel);
        $('.user_'+userID).attr("onclick","isActive("+newActiveDigit+","+userID+")");
      });
  }
</script>