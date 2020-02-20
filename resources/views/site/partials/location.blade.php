<section class="applySection location-section" style="padding-left: 15px;">
    <div class="row">
        <div class="col-sm-12">
            <h3>Locations willing to work</h3>
            <div class="alert alert-warning">
                Please add at least 1 location and at most 4.
            </div>
        </div>
    </div>
    <div class="sort" style="margin-left: -30px"></div>
    @if(count($user_location) <=3)
        <div class="row">
            <div class="col-sm-12">
                <form id="addlocation" action="{{URL::route('location-post')}}" method="post">
                    <input type="hidden" name="_token" value="{{{csrf_token() }}}">
                    <select class="form-control  loc-prof" name="location" id="location" required>
                        <option value="0">Select</option>
                        @foreach($location as $locate)
                            <option value="{{$locate->id}}">{{$locate->location}}</option>
                        @endforeach
                    </select>
                    <span class="pull-right" style="padding-right: 15px">
                        <button type="submit" class="btn btn-default  profi-edit add_location">Add</button>
                        <img id='processing_loader_resume_add' class='processing'
                             src="{{Config::get('app.url')}}/site/img/loading.gif"
                             style="display: none ;    position: absolute;margin-top: -44px;   margin-left: 57px;">
                    </span>
                </form>
            </div>
        </div>
    @endif
    <div class="row" id="location_container">
        @if(!empty($user_location))
            @foreach($user_location as $user_loc)
                <div id="location_{{$user_loc->id}}" class="added_location">
                    <div class="col-md-10 col-sm-10 col-xs-10 margprof-bott">
                        {{$user_loc->location}}
                    </div>
                    <div class="col-md-2 col-sm-2 col-xs-2">
                        <a class="pull-right" href="#" style="margin-right: 15px;">
                            <span class="  deletelocation" data-location-id="{{$user_loc->id}}"
                                          style="color: #a91b1b;">Remove</span>
                        </a>
                        <img id='processing_loader_resume_remove_{{$user_loc->id}}' class='processing'
                             src="{{Config::get('app.url')}}/site/img/loading.gif"
                             style="display: none; position: absolute; margin-top: 3px; margin-left: 100px;" />
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-md-12 col-sm-12  col-xs-12 margprof-bott">
                <h5 style="color:red;"> No Location Added</h5>
            </div>
        @endif
    </div>
</section>
