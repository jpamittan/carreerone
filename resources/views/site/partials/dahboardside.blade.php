<style>
.datetimepicker table tbody td:hover.unavailable {

   cursor: pointer !important;
}
</style>

<aside>
			<div class="col-xs-12 col-sm-6 col-md-3 paddingNone">
				<div class="col-xs-12 col-sm-12 col-md-12 asidepart paddingNone">
					<div class="col-xs-12 col-sm-12 col-md-12 upcoDiv paddingRight">
						<h3 class="upi">upcoming Interviews</h3>
					</div>
					@if(!empty($candidate_interview))
					@foreach($candidate_interview as $cand_inter)

					<div class="col-xs-12 col-sm-12 col-md-12 upcomingCurrentAlert paddingNone hoover">
						<div class="col-xs-3 col-sm-3 col-md-5"><h6>{{date("jS M  Y", strtotime($cand_inter->interview_date))}} {{$cand_inter->interview_time}}</h6></div>
						<div class="col-xs-7 col-sm-7 col-md-5"><h6 class="updesc">{{$cand_inter->job_title}} at {{$cand_inter->location}} in {{$cand_inter->category_name}}</h6></div>
						<div class="col-xs-2 col-sm-2 col-md-2"><h5><a href="{{URL::route('confirm-interview')}}">></a></h5></h5></div>
					</div>
						@endforeach
						@else
						<div class="col-md-12 upcomingAlert paddingNone">
							<div class="col-md-12"><h5 style="color: #d6034f;">No Interviews Scheduled</h5></div>
						</div>
						@endif
						<div class="col-xs-12 col-sm-12 col-md-12 paddingNone"><button class="seemorebtn"><a href="{{URL::route('confirm-interview')}}" style="color: white;">See more</a></button>
						</div>
				</div>
						<div class="col-xs-12 col-sm-12 col-md-12 paddingNone calenderdb">
							<div id="glob-data" data-toggle="calendar"></div>
						</div>

			<!-- <div class="col-xs-12 col-sm-12 col-md-12 firstDiv paddingNone"> -->
				<!-- <div class="col-xs-11 col-md-11 addmain paddingNone">
					<h4 class="adddb">Make sure you check your add your interview to your calender by clicking the icon</h4>
				</div> -->
				<!-- <div class="col-xs-12 col-md-12 imgad_d paddingNone">
					<img class="img-responsive" src="img/add.jpg" alt="add"/>
				</div> -->
			<!-- </div> -->
			<div class="col-xs-12 col-sm-12 col-md-12 paddingNone">
					<div class="col-xs-12 col-sm-12 col-md-12 carheading paddingNone">
						<h4 class="articles">articles</h4>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12 carouselArticles paddingNone">
						<div id="myCarousel" class="carousel slide" data-ride="carousel">
				   <!-- Indicators -->
					<ol class="carousel-indicators">
					  <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
					  <li data-target="#myCarousel" data-slide-to="1"></li>
					  <li data-target="#myCarousel" data-slide-to="2"></li>
					  <li data-target="#myCarousel" data-slide-to="3"></li>
					</ol>
					@php ($total = count($rss_feeds))
					@php ($pages = $total / 3)
                    <div class="carousel-inner" role="listbox">
                    @for($i = 0; $i < $total; $i++)
	                    @if(fmod($i, 3) == 0)
	                    	<div class="item <?php echo ($i == 0 ? ' active' :'');?>">
						@endif
		                        <div class="col-xs-12 col-sm-12 col-md-12 camaindiv paddingNone">
		                            <div class="col-xs-4 col-sm-4 col-md-4 thumbnail">
		                                <img src="{{$rss_feeds[$i]->thumbnail}}" alt="" class="imgWidth" />
		                            </div>
									<div class="col-xs-8 col-sm-8 col-md-8 caption">

										<h6 style="font-size: 10px;color: #655b5b;margin-top: 0px;">
										@php ($feed_date = date("Y-m-d", strtotime($rss_feeds[$i]->published)))
										{{date("jS M  Y", strtotime($feed_date))}}</h6>
										<h5 class="titlesHead">{{$rss_feeds[$i]->title}}</h5>
										<h6 style="margin-bottom: 0px;"><a href="{{$rss_feeds[$i]->link}}"  target="_blank">Read more</a></h6>
									</div>
		                        </div>
	                      		@if(fmod($i, 3) == 2)
	                     	</div>
						@endif
						@endfor
					</div>
				</div>
					</div>
				</div>
			</div>
			</div>
		</aside>
<script type="text/javascript" src="{{URL::to('/site/js/calander.js')}}"></script>
