 <?php use App\Models\Factories\Format; ?>
 <section class="applySection resume-upload">
            <div class="row res-marg">
               <div class="col-md-12 col-sm-12 col-xs-12  jobmatchYout paddingNone" >
                    <div class="col-md-12 col-sm-12 col-xs-12 sort">
                      <div class="col-md-12 col-sm-12 col-xs-12 head-marg">
                       <h3 class="">Resume</h3>
                          <div class="alert alert-warning">
                              Please upload a resume. Resumes uploaded to an occupational category will be used to match you to roles in that category. Resumes added as “Master” will be used for roles in any other category.
                          </div>
                    </div>




                </div>

                 @if(count($user_resume) <=4)
                     <form id="uploadResumeFile" action="{{URL::route('profile-file-upload')}}" method="post"  enctype='multipart/form-data'>

                         <div class="col-md-12 col-sm-12 col-xs-12   " style=" padding-top: 10px;margin-left:20px;width:98%">
                                <div class="row" style="padding-bottom: 10px;  ">
                                    <div class="col-md-4 col-sm-4 col-xs-11 widthclass">
                                        <select class="form-control  " name="category_resume" id="category_resume" required>
                                                <option value="501">Draft</option>
                                             <option value="500">Master</option>



                                                @foreach($allAvailableCategories as $category)
                                                    <option value="{{$category->id}}">{{$category->category_type_name}}</option>
                                                 @endforeach

                                        </select>
                                    </div>
                                    <div class="col-md-5 col-sm-5 col-xs-12 " style="padding-top : 5px;">
                                         <input type="file" class=" " id="inputfile" name="inputfile" >
                                    </div>
                                    <div class="col-md-2 col-sm-2 col-xs-12 ">
                                         <input type="hidden" name="_token" value="{{{csrf_token() }}}">

                                         <span class=" "><button type="submit" class="btn btn-default  uplo-edit upload_resume_btn" style="margin-top:5px;">Upload</button></span>
                                        <img id='processing_loader_resume_upload' class='processing' src="{{Config::get('app.url')}}/site/img/loading.gif" style="display: none; margin-top: 6px;   margin-left: 6px;">

                                    </div>

                                </div>
                         </div>

                      </form>
                  @endif
                    <div class="col-md-12 col-sm-12 col-xs-12  intervconfirm" style="margin-left:20px;width:98%">
                        <div class="row" style="padding-bottom: 10px; font-weight: bold">
                            <div class="col-md-5 col-sm-6 col-xs-6 ">
                            Name
                            </div>
                            <div class="col-md-3 col-sm-2 col-xs-2 ">
                             Category
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-2 ">
                             Uploaded
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-2 ">
                             Action
                            </div>

                         </div>

                          @if(!empty($user_resume))
                                @foreach($user_resume as $userresume)
                                 <div class="row" style="padding-bottom: 10px;">
                                        <div class="col-md-5 col-sm-6 col-xs-6 ">
                                         <a href="{{URL::to('/site/download_resume/'.$userresume->id )}}" id="download"> {{$userresume->resume_name}}</a>

                                        </div>
                                        <div class="col-md-3 col-sm-2 col-xs-2 ">
                                        @if($userresume->category_id == 500)
                                               Master
                                        @elseif($userresume->category_id == 501)
                                            Draft
                                        @else
                                          {{$userresume->category_name}}

                                        @endif
                                        </div>
                                        <div class="col-md-2 col-sm-2 col-xs-2 ">
                                        @if(!empty($userresume->created_at)){{ Format::displayDateTime($userresume->created_at  )  }}
                                                            @else&nbsp;@endif


                                        </div>
                                        <div class="col-md-2 col-sm-2 col-xs-2 ">

                                         <a href="#"><span class=" deleteresume"  data-resume-id="{{$userresume->id}}" style="color: #a91b1b;">Remove</span></a>
                                         <?php /*  <a href="{{URL::to('/site/download_resume/'.$userresume->id )}}" id="download"> View</a> */?>
                                        </div>
                                  </div>

                                @endforeach
                             @else
                               <h5 style="color:red;">No Resume Found</h5>

                            @endif



                    </div>

                </div>
            </div>
        </section>