<div class="navbar-wrapper container">
							<div class="container">
								<nav class="navbar navbar-default insNav" role="navigation"> 
									<div class="head">
										<div class="navbar-header">
											<!-- FOR MOBILE VIEW COLLAPSED BUTTON -->
											<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
											  <span class="sr-only">Toggle navigation</span>
											  <span class="icon-bar"></span>
											  <span class="icon-bar"></span>
											  <span class="icon-bar"></span>
											</button>
											<!-- LOGO -->
											<!-- TEXT BASED LOGO -->
										   <!--  <a class="navbar-brand" href="#">MAYA<span>Pro</span></a> -->           
											<!-- IMG BASED LOGO  -->
											 <a class="navbar-brand" href="/"><img class="ins_logo" src="/site/img/ins-logo.png" alt="logo"></a>   
										</div>
										<ul class="nav navbar-nav navbar-right" style="margin-right: 17px;">
                         @if(Auth::check())
                        <li>
                        <a href="{{URL::route('site-logout')}}" class="btn btn-default btn-flat ins-logout">Log Out</a>
                        </li> 
                        @endif
                    <!-- <li><a href="#">HI Name</a></li> -->
                    </ul>
									</div>     
								</nav>
							</div>
						</div>