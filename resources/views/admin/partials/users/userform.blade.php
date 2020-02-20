<div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">{{ !empty($details)?'Edit User':'Create User' }}</h3>
            </div>
            <!-- /.box-header -->
             <!-- error -->
            <div class="error" style="color: red;">
              
            </div>
           <!-- /error -->
            <!-- form start -->
            <form  id="ins-frm" name='ins-frm'  role="form" action="#" method="POST">
            <input type="hidden" name="_token" id="token" value="{{{csrf_token() }}}">
              <div class="col-md-12 box-body userform">
              <div class="col-md-12">
              <div class="col-md-3 form-group">
                  <label for=>First name</label>
                  <input class="form-control" id="first_name" name="first_name" value="{{ !empty($details->first_name)?$details->first_name:'' }}" placeholder="First Name" type="text">
                </div>
                <div class="col-md-3 form-group">
                  <label >Last name</label>
                  <input class="form-control" id="last_name" name="last_name" value="{{ !empty($details->last_name)?$details->last_name:'' }}" placeholder="Last Name" type="text">
                </div>
              </div>
              @if(empty($details))
               <div class="col-md-12">
               <div class="col-md-3 form-group">
                  <label for="exampleInputEmail1">Email</label>
                  <input class="form-control" id="email" name="email" value="{{ !empty($details->email)?$details->email:'' }}" placeholder="Enter email" type="email">
                </div>
                <div class="col-md-3 form-group">
                  <label >Password</label>
                  <input class="form-control" id="password" name="password" placeholder="Password" type="password">
                </div>
              </div> 
              @endif
              <div class="col-md-12">
               <div class="col-md-6 form-group">
                  <label for="exampleInputEmail1">Role</label><br>
                  <select class="form-group" id="role" name='role'>
                      <option value="-1">Select</option>
                        @if(!empty($details))
                        <option value="1" {{ !empty($details->role_id==1)?'selected=selected':'' }} >Admin</option>
                        <option value="2" {{ !empty($details->role_id==2)?'selected=selected':'' }}>Case Manager</option>
                        <option value="3" {{ !empty($details->role_id==3)?'selected=selected':'' }}>Candidate</option>
                        <option value="4" {{ !empty($details->role_id==4)?'selected=selected':'' }}>Recruiter</option>
                        @else
                        <option value="1">Admin</option>
                        <option value="2">Case Manager</option>
                        <option value="3">Candidate</option>
                        <option value="4">Recruiter</option>
                        @endif
                      </select>

                </div>
                
              </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="button" id="create-form" class="btn btn-primary">Submit</button>
                <input type="hidden" value="{{ !empty($details)?'edit':'new' }}" name="event_value" />
              </div>
            </form>
          </div>

    <script>
$(function() {

  $("#create-form").click(function(e){
   e.preventDefault();
   e.stopPropagation();
    
    $.ajax({
      type: "POST",
      url: "{{URL::route('admin-create-form')}}",
      data: $("form[name='ins-frm']").serialize()+"&id={{ !empty($details->id)?$details->id:'' }}",
      success: function(msg){
        if(msg.success== false){
          $(".error").html(msg.errors);
        }else{
            location.reload("{{URL::to('admin/home')}}");
        }
      },

        error: function(jqXHR, textStatus, errorThrown) {
         $errors =  jQuery.parseJSON(jqXHR.responseText);
            $(".error").html($errors.errors.email);
            $(".error").html($errors.errors.password);
        }
    });
  });
});

</script>


