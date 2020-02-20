
 <div class="row jobmatchYout" style="padding-top:15px; padding-bottom: 15px; text-align: center;">
     
<?php
               $jobCount =  $careeronejobs['jobCount'];
              $jobs =  $careeronejobs['jobs'];
                $params =  $careeronejobs['params'];
               $fullparams =  $careeronejobs['fullparams'];
              $paginator =  $careeronejobs['paginator'];
                 
             $query =  $careeronejobs['query'];

             $fullparams = str_replace('ctabval=insjobs&',"", $fullparams);
                $fullparams = str_replace('ctabval=careeronejobs&',"", $fullparams);
                  $fullparams = str_replace('ctabval=matchedjobs&',"", $fullparams);
                  
?>  
 

@if($paginator)
        @if($paginator->total > $paginator->perpage)
            <ul class="pagination">
                <?php
              
                     $disabled='';
                     $dsiabledurl = "/site/dashboard?ctabval=careeronejobs&page=".  ($paginator->currentpage -1 )."&".$fullparams;
                     if($paginator->currentpage == 1){
                             $disabled='disabled';
                             $dsiabledurl="javascript:void(0)";
                             
                     }
         
                        $lastpage = false;
                     $disabledlast='';
                     $totalpagenumber = ceil(  $paginator->total /  $paginator->perpage )  ;
                     $dsiabledurllast = "/site/dashboard?ctabval=careeronejobs&page=".  ($paginator->currentpage +1 )."&".$fullparams;;
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
                                <li class="{{$isactive}}"><a href="/site/dashboard?ctabval=careeronejobs&page={{$i}}&{{$fullparams}}">{{$i}}</a></li>
                    <?php
                             }
                          }
                    ?>

                <li class="{{$disabledlast}}"><a href="{{$dsiabledurllast}}">&raquo;</a></li>
                

            </ul>

    @endif

   
@endif
 </div> 