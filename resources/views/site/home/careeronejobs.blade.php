@extends('site.layouts.master-dashboard')
<style>

.twitter-typeahead {
    width: 100%;
}
.twitter-typeahead .tt-menu {
    width: 100%;
}
.twitter-typeahead .tt-menu .tt-dataset .tt-selectable {
    background-color: #FFF;
    color: #333;
    padding: 10px;
    font-weight: normal;
    cursor: pointer;
}
.twitter-typeahead .tt-menu .tt-dataset .tt-selectable:hover {
    background-color: #673894 !important;
    color: #FFF !important;
}

.scrollable_search_dropdown .tt-menu {
    max-height: 200px;
    width: 100%;
    overflow-y: auto;
}

#location-prefill {
    position: absolute;
    font-size: 20px;
    right: 8px;
    top: 12px;
    color: #cccccc;
}

</style>
@section('content')

    <div class="container">
    <div class="row1">
  <div class="col-md-12 dbmaindas">
    <!-- success -->
     
 <section>
    <form action='/site/careeronejobs' method="GET">
    <div class="row">
         <div class="col-xs-12 col-sm-12 col-md-12 jobmatchYout paddingNone">
            <div class="col-xs-12 col-sm-12 col-md-12  " style="padding-top:10px; padding-bottom:20px;">
                
                <div class="col-sm-12 col-md-3  ">
                

                   <input id="search_keywords"   class="form-control" name="search_keywords" placeholder="Role Title" value="{{  isset($params['search_keywords']) ? $params['search_keywords'] : '' }}" type="text">
                </div>


                <div class="col-sm-12 col-md-3  ">
                    <select id="search_category" class="form-control  " name="search_category">
                        <option value="">Category</option>
                        <option value="1" {{  (isset($params['search_category']) && ($params['search_category']  == 1  )) ? 'selected="selected"' : '' }}>Accounting</option>
                        <option value="2" {{  (isset($params['search_category']) && ($params['search_category']  == 2  )) ? 'selected="selected"' : '' }}>Administration & Secretarial</option>
                        <option value="11454" {{  (isset($params['search_category']) && ($params['search_category']  == 11454  )) ? 'selected="selected"' : '' }}>Advertising, Media, Arts & Entertainment</option>
                        <option value="15425" {{  (isset($params['search_category']) && ($params['search_category']  == 15425  )) ? 'selected="selected"' : '' }}>Agriculture, Nature & Animal</option>
                        <option value="558" {{  (isset($params['search_category']) && ($params['search_category']  ==558  )) ? 'selected="selected"' : '' }}>Banking & Finance</option>
                        <option value="559" {{  (isset($params['search_category']) && ($params['search_category']  == 559  )) ? 'selected="selected"' : '' }}>Biotech, R&D, Science</option>
                        <option value="11741" {{  (isset($params['search_category']) && ($params['search_category']  == 11741  )) ? 'selected="selected"' : '' }}>Career Expos</option>
                        <option value="544" {{  (isset($params['search_category']) && ($params['search_category']  == 544  )) ? 'selected="selected"' : '' }}>Construction, Architecture & Interior Design</option>
                        <option value="545" {{  (isset($params['search_category']) && ($params['search_category']  == 545 )) ? 'selected="selected"' : '' }}>Customer Service & Call Centre</option>
                        <option value="5623" {{  (isset($params['search_category']) && ($params['search_category']  == 5623  )) ? 'selected="selected"' : '' }}>Editorial & Writing</option>
                        <option value="3" {{  (isset($params['search_category']) && ($params['search_category']  == 3 )) ? 'selected="selected"' : '' }}>Education, Childcare & Training</option>
                        <option value="4" {{  (isset($params['search_category']) && ($params['search_category']  == 4  )) ? 'selected="selected"' : '' }}>Engineering</option>
                        <option value="3561" {{  (isset($params['search_category']) && ($params['search_category']  == 3561  )) ? 'selected="selected"' : '' }}>Executive & Strategic Management</option>
                        <option value="11814" {{  (isset($params['search_category']) && ($params['search_category']  == 11814  )) ? 'selected="selected"' : '' }}>Franchise & Business Ownership</option>
                        <option value="15431" {{  (isset($params['search_category']) && ($params['search_category']  == 15431  )) ? 'selected="selected"' : '' }}>Government, Defence & Emergency</option>
                        <option value="3975" {{  (isset($params['search_category']) && ($params['search_category']  == 3975  )) ? 'selected="selected"' : '' }}>Health, Medical & Pharmaceutical</option>
                        <option value="13" {{  (isset($params['search_category']) && ($params['search_category']  == 13  )) ? 'selected="selected"' : '' }}>Hospitality, Travel & Tourism</option>
                        <option value="5" {{  (isset($params['search_category']) && ($params['search_category']  == 5  )) ? 'selected="selected"' : '' }}>HR & Recruitment</option>
                        <option value="15426" {{  (isset($params['search_category']) && ($params['search_category']  == 15426  )) ? 'selected="selected"' : '' }}>Insurance & Superannuation</option>
                        <option value="660" {{  (isset($params['search_category']) && ($params['search_category']  == 660  )) ? 'selected="selected"' : '' }}>IT</option>
                        <option value="7" {{  (isset($params['search_category']) && ($params['search_category']  == 7 )) ? 'selected="selected"' : '' }}>Legal</option>
                        <option value="5625" {{  (isset($params['search_category']) && ($params['search_category']  == 5625  )) ? 'selected="selected"' : '' }}>Logistics, Supply & Transport</option>
                        <option value="47" {{  (isset($params['search_category']) && ($params['search_category']  == 47 )) ? 'selected="selected"' : '' }}>Manufacturing & Industrial</option>
                        <option value="9007" {{  (isset($params['search_category']) && ($params['search_category']  == 9007  )) ? 'selected="selected"' : '' }}>Marketing</option>
                        <option value="15430" {{  (isset($params['search_category']) && ($params['search_category']  == 15430  )) ? 'selected="selected"' : '' }}>Mining, Oil & Gas</option>
                        <option value="11" {{  (isset($params['search_category']) && ($params['search_category']  == 11  )) ? 'selected="selected"' : '' }}>Other</option>
                        <option value="9008" {{  (isset($params['search_category']) && ($params['search_category']  == 9008  )) ? 'selected="selected"' : '' }}>Program & Project Management</option>
                        <option value="15427" {{  (isset($params['search_category']) && ($params['search_category']  == 15427 )) ? 'selected="selected"' : '' }}>Property & Real Estate</option>
                        <option value="11455" {{  (isset($params['search_category']) && ($params['search_category']  == 11455  )) ? 'selected="selected"' : '' }}>Quality Assurance & Safety</option>
                        <option value="15428" {{  (isset($params['search_category']) && ($params['search_category']  == 15428  )) ? 'selected="selected"' : '' }}>Retail</option>
                        <option value="10" {{  (isset($params['search_category']) && ($params['search_category']  == 10  )) ? 'selected="selected"' : '' }}>Sales</option>
                        <option value="555" {{  (isset($params['search_category']) && ($params['search_category']  == 555  )) ? 'selected="selected"' : '' }}>Security & Protective Services</option>
                        <option value="553" {{  (isset($params['search_category']) && ($params['search_category']  == 553  )) ? 'selected="selected"' : '' }}>Trades & Services</option>
                        <option value="15429" {{  (isset($params['search_category']) && ($params['search_category']  == 15429 )) ? 'selected="selected"' : '' }}>Voluntary, Charity & Social Work</option>
                        <option value="12008" {{  (isset($params['search_category']) && ($params['search_category']  == 12008  )) ? 'selected="selected"' : '' }}>Work from Home</option>
                        </select>
                </div>


                <div class="col-sm-12 col-md-3 ">
                    <input id="search_location"  class="form-control"  name="search_location" placeholder="Location" value="{{  isset($params['search_location']) ? $params['search_location'] : '' }}" type="text">
                </div>



                <div class="col-sm-12 col-md-3 ">
                  <input class="btn btn-primary " href="#" role="button" value="Search" type="submit">
                </div>


            </div>
            
        </div>
    </div>
    </form>
</section> 
 
  
   <!-- /success -->    
        @include('site.partials.careeronejobs', array('jobs' => $jobs ))
         

      
    </div>

 <div class="row jobmatchYout" style="padding-top:15px; padding-bottom: 15px; text-align: center;">
      
 

@if($paginator)
        @if($paginator->total > $paginator->perpage)
            <ul class="pagination">
                <?php
              
                     $disabled='';
                     $dsiabledurl = "/site/careeronejobs?page=".  ($paginator->currentpage -1 )."&".$fullparams;
                     if($paginator->currentpage == 1){
                             $disabled='disabled';
                             $dsiabledurl="javascript:void(0)";
                             
                     }
         
                        $lastpage = false;
                     $disabledlast='';
                     $totalpagenumber = ceil(  $paginator->total /  $paginator->perpage )  ;
                     $dsiabledurllast = "/site/careeronejobs?page=".  ($paginator->currentpage +1 )."&".$fullparams;;
                     if($paginator->currentpage >=  ($paginator->total/20)){
                             $disabledlast='disabled';
                             $dsiabledurllast="javascript:void(0)";
                                $lastpage = true;
                     }

                ?>

                <li class="{{$disabled}}"><a href="{{$dsiabledurl}}">&laquo;</a></li>

                    <?php
                        $counter =1;
                        if($paginator->currentpage >= 10){
                            $counter =  $counter + 10;
                            $counter =  $paginator->currentpage + 1;
                        }
                          $till =($counter + $paginator->perpage) - 10;
                          if($lastpage ){
                                 if(count($jobs) < $paginator->perpage){
                                   //  $counter =  ceil($paginator->total/20) - 4 ;

                                     //echo $till = ceil(  $paginator->total /  $paginator->perpage )  +1;
                                 }else{
                                     $counter =  ceil($paginator->total/20) - 9;
                                     $till =(($counter ) + $paginator->perpage) - 10;
                                 }
                          }
  
                        for($i= $counter ;$i<  $till  ;$i++){

                            $isactive='';
                            if($i == $paginator->currentpage ){
                                $isactive='active';
                            }
                            if($i<= $totalpagenumber){
                    ?>  
                                <li class="{{$isactive}}"><a href="/site/careeronejobs?page={{$i}}&{{$fullparams}}">{{$i}}</a></li>
                    <?php
                             }
                          }
                    ?>

                <li class="{{$disabledlast}}"><a href="{{$dsiabledurllast}}">&raquo;</a></li>
                

            </ul>

    @endif

   
@endif

    


    </div> 



    </div>
    <script type="text/javascript" src="https://cdn.careeronecdn.com.au/lux/careerone/third_party/typeahead.min.js"></script>


<Script>
$('#search_location').typeahead({
        highlight: true,
        minLength: 3,
    }, {
        source: function (query, syncResults, asyncResults) {
            $.get('/site/search/getAreas', {
                lids: '',
                keyword: query,
            }, function (data) {
                asyncResults(data);
            });
        },
        limit: 5,
        name: 'regions'
    }, {
        source: function (query, syncResults, asyncResults) {
            $.get(  '/site/search/location/autocomplete', {
                lids: '',
                query: query,
            }, function (data) {
                var suburbs = [];
                for (k in data.suggestions) {
                    suburbs.push(data.suggestions[k].value);
                }
                asyncResults(suburbs);
            });
        },
        limit: 5,
        name: 'suburbs'
    });

    $('#search_location').on('change, typeahead:selected', function(event) {
        $('#location-prefill').hide();
    });

</Script>
</body>

</html>
            
@endsection
