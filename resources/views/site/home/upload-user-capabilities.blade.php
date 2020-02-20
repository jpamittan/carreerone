@extends('site.layouts.master-no-header-footer')

@section('content')

					<section>
					<div class="container apply-paddingnone" style="background-color: white;">
						<div class = "col-md-12  bck-color">
							<div class= "col-xs-12 col-sm-12 col-md-12 jobview-header">
							 
								
								<div class ="col-xs-12 col-sm-12 col-md-12 form-margin1">
									
									 
									 
									<!-- <div class= "col-xs-2 col-sm-12 col-md-4">
										<div class = "accordion-group accordion-caret">
										        <span class = "pull-right form-border1"><a class="accordion-toggle " data-toggle="collapse" href="#collapseOne"></a></span>
										</div>
									</div> -->
								</div>
									
								   	<div class ="col-xs-12 col-sm-12 col-md-12 acc-marg">

										<div id="collapseOne" class="pull-left accordion-inner accordion-body accord-apply collapse in">
											<!-- error -->
											@if(!empty(\Session::get('error')))
											<div class="alert alert-danger">
											   {{\Session::get('error')}}
											</div>
											@endif
						           <!-- /error -->
												<!-- <form role="form"> -->
												<form id="ins-frm" name="ins-frm" action="{{URL::route('post-upload-user-capabilities',['crm_user_id' => $user->crm_user_id]) }}" method="post" enctype='multipart/form-data'> 
									 				<!-- <input type="hidden" name="_token" id="token" value="{{{csrf_token() }}}"> --> 
													<div class="col-sm-12 col-md-12 " >
														<h5><span class="uploadResume">Upload EiT Role Description 
															@if(!empty($user->first_name))
																 	{{$user->first_name}}
															@endif

														</span> </h5>
														 <div class="col-xs-12 col-sm-12 col-md-12 browse-width">
													       <input type="file" class="form-control form-ctrl1" id="inputfile" name="inputfile" value = "No File Choosen">
														   
													    </div>
													  
													</div>
													<div class="buttonjobview">
													<button type="submit" id="applybutton5"  name="status" class="btn btn-primary rd-sub">Submit</button>
													
													</div>
											</form>
												<!-- </form> -->
									    </div>
									</div>
								 
									
								</div>
								 
							</div>
						</div>
				</section>
						
@endsection
