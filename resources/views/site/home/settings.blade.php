@extends('site.layouts.master-dashboard')

@section('content')
	
					<section>
					<div class = "container container-whitebg">
						<div class = "col-md-12 bck-color">
							<div class="col-xs-12 col-sm-12 col-md-12 jobview-header" style="height: 200px; text-align:center; margin-top: 108px;">
								 <form id="uploadfile" action="{{URL::to('site/upload_role_description',5)}}" method="post"  enctype='multipart/form-data'>
                     <input type="hidden" name="_token" value="{{{csrf_token() }}}">
                    <input type="file" class="lo-prof" id="file" name="file">
                        <span class="pull-right"><button type="submit" class="btn btn-default  uplo-edit">Upload</button></span>
                        </form>
								
								</div>
								
							</div>
						</div>
				</section>
						
@endsection
