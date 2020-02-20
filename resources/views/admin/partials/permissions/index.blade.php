
<link rel="stylesheet" href="{{ asset("/admin/plugins/datatables/dataTables.bootstrap.css") }}">
<div class="box">
            <div class="box-header">
              <h3 class="box-title"><b>Roles</b></h3>
              <button class="pull-right" id="create-user" style="background-color: #3c8dbc;color: white;width: 87px;    font-size: 19px;">Create</button>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap"><div class="row"><div class="col-sm-12"><table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                <thead>
                <tr role="row">
                <th>Display Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
                </tr>
                </thead>
                <tbody>
                  <tr role="row" class="odd">
                    <td class="sorting_1">Gecko</td>
                    <td>Firefox 1.0</td>
                    <td>Win 98+ / OSX.2+</td>
                    <td>1.7</td>
                    <td>Edit</td>
                  </tr>
                  <tr role="row" class="even">
                    <td class="sorting_1">Gecko</td>
                    <td>Firefox 1.5</td>
                    <td>Win 98+ / OSX.2+</td>
                    <td>1.8</td>
                    <td>Edit</td>
                  </tr>
                </tbody>
                
              </table></div></div></div>
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
  $('#create-user').click(function (e) {
    e.preventDefault();
        $('.content').html('');
        $.ajax({
            url: "<?php echo url('admin/permissions'); ?>",
            type: "GET",
            success: function (data) {
                $('.content').html(data);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                //what to do in error
            },
            timeout: 15000//timeout of the ajax call
        });

//    

});
</script>