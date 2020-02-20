<div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Create User</h3>
            </div>
            <!-- /.box-header -->
             <!-- error -->
            <div class="error" style="color: red;">
              
            </div>
           <!-- /error -->
            <!-- form start -->
            <form  id="ins-frm"  role="form" action="#" method="POST">
            <input type="hidden" name="_token" id="token" value="{{{csrf_token() }}}">
              <div class="col-md-12 box-body userform">
              <div class="col-md-2">
              <h3>#</h3>
              </div>
              <div class="col-md-2">
              <h3>Admin</h3>
              </div>
              <div class="col-md-2">
              <h3>Case Manager</h3>
              </div>
              <div class="col-md-2">
              <h3>Candidtae</h3>
              </div>
              <div class="col-md-2">
              <h3>Recruiter</h3>
              </div>
            
              </div>
              <div id="permission-container">
              
              </div>
              <!-- /.box-body -->

             <div class="box-footer" >
                <button type="button" id="create-form" class="btn btn-primary">Submit</button>
              </div>
            </form>
          </div>
          <div id="permission" class="col-md-12 box-body" style="display:none">
              <div class="col-md-2">
              <h3>Role add</h3><input type="hidden" id="name" class="name_id" value="">
              </div>
              <div class="col-md-2">
              <h3><input type="checkbox" class="admin" value="1"></h3>
              </div>
              <div class="col-md-2">
               <h3><input type="checkbox" class="cm" value="2"></h3>
              </div>
              <div class="col-md-2">
               <h3><input type="checkbox" class="can" value="3"></h3>
              </div>
              <div class="col-md-2">
               <h3><input type="checkbox" class="rec" value="4"></h3>
              </div>
            
              </div>
<script>
  $( document ).ready(function() {
  $.ajax({
      type: "GET",
      url: "{{URL::route('admin-role-permissions')}}",
      success: function(data){
        $(data.permissions).each(function(index,permission) {
        var html = $('#permission').clone();
        html.find('input[type=checkbox]').attr('name','role_check['+permission.id+'][]');
        html.show().attr('id',null);
        html.find('.name_id').val(permission.id).prev().text(permission.name);
        html.appendTo($('#permission-container'));
    });
      },
        error: function() {
         }
    });
  });
</script>
    <script>
$(function() {
  

  $("#create-form").click(function(e){
   e.preventDefault();
      var formdata = $( "#ins-frm" ).serialize();
      alert(formdata);
    $.ajax({
      type: "POST",
      url: "{{URL::route('admin-post-permissions')}}",
      data:formdata,
      success: function(msg){
        
        
      
      },
        error: function(msg) {
        alert('failure');
        }
    });
  });
});

</script>


