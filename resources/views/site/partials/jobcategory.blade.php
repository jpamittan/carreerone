<section class="applySection category-section" style="padding-left: 15px;">
    <div class="row">
        <div class="col-sm-12">
            <h3>Occupational categories</h3>
            <div class="alert alert-warning">
                Please add at least 1 occupational category and at most 8. Please only add categories you have relevant qualifications or genuine experience in.
                These categories will be used to shortlist appropriate roles for your skill set.
            </div>
        </div>
    </div>
    <div class="sort" style="margin-left: -30px"></div>
    @if(count($user_category) <=7)
        <div class="row">
            <div class="col-sm-12">
                <form method="post" action="{{URL::route('site-post-category')}}" id='add-category-frm'>
                    <input type="hidden" name="_token" value="{{{csrf_token() }}}">
                    <input type="hidden" name="popup-category" id="popup-category"
                           value="{{$user->profile_completion_email}}">
                    <select class="form-control  loc-prof" name="category" id="sel1category" required>
                        <option value="0">Select</option>
                        @foreach($category as $cate)
                            <option value="{{$cate->id}}">{{$cate->category_type_name}}</option>
                        @endforeach
                    </select>
                    <span class="pull-right" style="padding-right: 15px">
                        <button type="button" data-toggle="modal"
                                class="btn btn-default profi-edit add-category">Add</button>
                        <img id='processing_loader_occupational_category' class='processing'
                             src="{{Config::get('app.url')}}/site/img/loading.gif"
                             style="display: none ;    position: absolute;margin-top: -44px;   margin-left: 57px;"/>
                        </span>
                </form>
            </div>
        </div>
    @endif
    <div class="row">
        @if(!empty($user_category))
            @foreach($user_category as $category)
                <div class="col-md-10 col-sm-10 col-xs-10 margprof-bott">
                    {{$category->category_type_name}}
                </div>
                <div class="col-md-2 col-sm-2 col-xs-2 margprof-bott">
                    @if($category->pending == 1 )
                        <span class="pull-right" href="#" style="margin-right: 15px;">Pending</span>
                    @else
                        <a class="pull-right" href="#" style="margin-right: 15px;">
                            <span class="deletecategory" data-category-id="{{$category->job_category_type_id}}"
                                  style="color: #a91b1b;">Remove</span>
                        </a>
                    @endif
                </div>
            @endforeach
        @else
            <div class="col-md-12 col-sm-12 col-xs-12 margprof-bott">
                <h5 class="text-danger"> No Category Added. Please select at least one occupational category</h5>
            </div>
        @endif
    </div>
</section>


<!-- Modal -->
<div id="myModalCategory" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <form id="add-category-form" role="form" method="post" action="{{URL::route('site-post-category')}}">
            <input type="hidden" name="_token" id="token" value="{{{csrf_token() }}}"/>
            <input type="hidden" name="category_id" id="category_id" value=""/>
            <input type="hidden" name="popup-category-val" id="popup-category-val" value=""/>
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Please enter a reason to add this category</h4>
                </div>
                <div class="modal-body">
                    <p>
                        <textarea id='add_category_txt' name='add_category_txt'
                                  style="width:100%; height:100px"></textarea>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="add_category_cancel" class="btn btn-default" data-dismiss="modal">Cancel
                    </button>
                    <button id="add_category_btn" class="btn btn-default">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>


         


