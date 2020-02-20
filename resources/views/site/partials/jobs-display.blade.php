<?php
  $params = $careeronejobs['params'];
  if(!isset($params['ctabval'])){
    $params['ctabval'] = 'insjobs';
  }
?>
<section>
  <form action='/site/dashboard' method="GET">
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 jobmatchYout paddingNone">
        <div id='searchfilter' class="col-xs-12 col-sm-12 col-md-12" style="padding-top:10px; padding-bottom:20px;">
          <div class="col-sm-12 col-md-3">
            <input id="ctabval" name="ctabval" value="{{isset($params['ctabval']) ? $params['ctabval'] : ''}}" type="hidden">
            <input id="search_keywords" class="form-control" name="search_keywords" placeholder="Role Title" value="{{  isset($params['search_keywords']) ? $params['search_keywords'] : '' }}" type="text">
          </div>
          <div class="col-sm-12 col-md-3">
            <select id="search_category" class="form-control" name="search_category">
              <option value="">Category</option>
              <option value="1" {{(isset($params['search_category']) && ($params['search_category']  == 1)) ? 'selected="selected"' : '' }}>Accounting</option>
              <option value="2" {{(isset($params['search_category']) && ($params['search_category']  == 2)) ? 'selected="selected"' : '' }}>Administration &amp; Secretarial</option>
              <option value="11454" {{(isset($params['search_category']) && ($params['search_category']  == 11454)) ? 'selected="selected"' : '' }}>Advertising, Media, Arts &amp; Entertainment</option>
              <option value="15425" {{(isset($params['search_category']) && ($params['search_category']  == 15425)) ? 'selected="selected"' : '' }}>Agriculture, Nature &amp; Animal</option>
              <option value="558" {{(isset($params['search_category']) && ($params['search_category']  == 558)) ? 'selected="selected"' : '' }}>Banking &amp; Finance</option>
              <option value="559" {{(isset($params['search_category']) && ($params['search_category']  == 559)) ? 'selected="selected"' : '' }}>Biotech, R&amp;D, Science</option>
              <option value="11741" {{(isset($params['search_category']) && ($params['search_category']  == 11741)) ? 'selected="selected"' : '' }}>Career Expos</option>
              <option value="544" {{(isset($params['search_category']) && ($params['search_category']  == 544)) ? 'selected="selected"' : '' }}>Construction, Architecture &amp; Interior Design</option>
              <option value="545" {{(isset($params['search_category']) && ($params['search_category']  == 545)) ? 'selected="selected"' : '' }}>Customer Service &amp; Call Centre</option>
              <option value="5623" {{(isset($params['search_category']) && ($params['search_category']  == 5623)) ? 'selected="selected"' : '' }}>Editorial &amp; Writing</option>
              <option value="3" {{(isset($params['search_category']) && ($params['search_category']  == 3)) ? 'selected="selected"' : '' }}>Education, Childcare &amp; Training</option>
              <option value="4" {{(isset($params['search_category']) && ($params['search_category']  == 4)) ? 'selected="selected"' : '' }}>Engineering</option>
              <option value="3561" {{(isset($params['search_category']) && ($params['search_category']  == 3561)) ? 'selected="selected"' : '' }}>Executive &amp; Strategic Management</option>
              <option value="11814" {{(isset($params['search_category']) && ($params['search_category']  == 11814)) ? 'selected="selected"' : '' }}>Franchise &amp; Business Ownership</option>
              <option value="15431" {{(isset($params['search_category']) && ($params['search_category']  == 15431)) ? 'selected="selected"' : '' }}>Government, Defence &amp; Emergency</option>
              <option value="3975" {{(isset($params['search_category']) && ($params['search_category']  == 3975)) ? 'selected="selected"' : '' }}>Health, Medical &amp; Pharmaceutical</option>
              <option value="13" {{(isset($params['search_category']) && ($params['search_category']  == 13)) ? 'selected="selected"' : '' }}>Hospitality, Travel &amp; Tourism</option>
              <option value="5" {{(isset($params['search_category']) && ($params['search_category']  == 5)) ? 'selected="selected"' : '' }}>HR &amp; Recruitment</option>
              <option value="15426" {{(isset($params['search_category']) && ($params['search_category']  == 15426)) ? 'selected="selected"' : '' }}>Insurance &amp; Superannuation</option>
              <option value="660" {{(isset($params['search_category']) && ($params['search_category']  == 660)) ? 'selected="selected"' : '' }}>IT</option>
              <option value="7" {{(isset($params['search_category']) && ($params['search_category']  == 7)) ? 'selected="selected"' : '' }}>Legal</option>
              <option value="5625" {{(isset($params['search_category']) && ($params['search_category']  == 5625)) ? 'selected="selected"' : '' }}>Logistics, Supply &amp; Transport</option>
              <option value="47" {{(isset($params['search_category']) && ($params['search_category']  == 47)) ? 'selected="selected"' : '' }}>Manufacturing &amp; Industrial</option>
              <option value="9007" {{(isset($params['search_category']) && ($params['search_category']  == 9007)) ? 'selected="selected"' : '' }}>Marketing</option>
              <option value="15430" {{(isset($params['search_category']) && ($params['search_category']  == 15430)) ? 'selected="selected"' : '' }}>Mining, Oil &amp; Gas</option>
              <option value="11" {{(isset($params['search_category']) && ($params['search_category']  == 11)) ? 'selected="selected"' : '' }}>Other</option>
              <option value="9008" {{(isset($params['search_category']) && ($params['search_category']  == 9008)) ? 'selected="selected"' : '' }}>Program &amp; Project Management</option>
              <option value="15427" {{(isset($params['search_category']) && ($params['search_category']  == 15427)) ? 'selected="selected"' : '' }}>Property &amp; Real Estate</option>
              <option value="11455" {{(isset($params['search_category']) && ($params['search_category']  == 11455)) ? 'selected="selected"' : '' }}>Quality Assurance &amp; Safety</option>
              <option value="15428" {{(isset($params['search_category']) && ($params['search_category']  == 15428)) ? 'selected="selected"' : '' }}>Retail</option>
              <option value="10" {{(isset($params['search_category']) && ($params['search_category']  == 10)) ? 'selected="selected"' : '' }}>Sales</option>
              <option value="555" {{(isset($params['search_category']) && ($params['search_category']  == 555)) ? 'selected="selected"' : '' }}>Security &amp; Protective Services</option>
              <option value="553" {{(isset($params['search_category']) && ($params['search_category']  == 553)) ? 'selected="selected"' : '' }}>Trades &amp; Services</option>
              <option value="15429" {{(isset($params['search_category']) && ($params['search_category']  == 15429)) ? 'selected="selected"' : '' }}>Voluntary, Charity &amp; Social Work</option>
              <option value="12008" {{(isset($params['search_category']) && ($params['search_category']  == 12008)) ? 'selected="selected"' : '' }}>Work from Home</option>
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
<?php
  $inshistoryalljobs_count = 0;
  $insjobs_count = 0;
  $t_count = 0 ;
  if(isset($insjobs['jobCount'])) { $insjobs_count = $insjobs['jobCount']; }
  if(isset($inshistoryalljobs['jobCount'])) { $inshistoryalljobs_count = $inshistoryalljobs['jobCount']; }
  $t_count = $inshistoryalljobs_count + $insjobs_count;
?>
<section>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12  ">
      <ul class="nav nav-tabs">
        <li class="{{(isset($params['ctabval'])  && $params['ctabval'] == 'insjobs' ) ? 'active' : ''}}">
          <a data-toggle="tab" href="#insjobs" id="ctab" data-val="insjobs">
            Mobility Pathway Roles
            @if(isset($t_count))
              ({{$t_count}})
            @endif
          </a>
        </li>
        <li class="{{(isset($params['ctabval'])  && $params['ctabval'] == 'matchedjobs' ) ? 'active' : ''}}">
          <a data-toggle="tab" href="#matchedjobs" id="ctab" data-val="matchedjobs">
            Application Monitor
            @if(isset($insmatchedjobs['jobCount']))
              ({{$insmatchedjobs['jobCount']}})
            @endif
          </a>
        </li>
        <li class="{{(isset($params['ctabval'])  && $params['ctabval'] == 'careeronejobs' ) ? 'active' : ''}}">
          <a data-toggle="tab" href="#careeronejobs" id="ctab" data-val="careeronejobs">CareerOne Roles</a>
        </li>
      </ul>
    </div>
  </div>
</section>
<div class="tab-content">
  <div id="matchedjobs" class="tab-pane fade {{(isset($params['ctabval']) && $params['ctabval'] == 'matchedjobs' ) ? 'active in' : ''}}">
    @include('site.partials.insmatchedjobs', array('careeronejobs' => $insmatchedjobs ))
    @include('site.partials.paginator-insmatched', array('careeronejobs' => $insmatchedjobs ))
  </div>
  <div id="insjobs" class="tab-pane fade {{(isset($params['ctabval']) && $params['ctabval'] == 'insjobs' ) ? 'active in ' : ''}}">
    @include('site.partials.insjobs', array('careeronejobs' => $insjobs))
    @include('site.partials.insjobs-match-history', array('careeronejobs' =>$inshistoryalljobs))
    @include('site.partials.paginator-ins', array('careeronejobs' => $insjobs))
  </div>
  <div id="careeronejobs" class="tab-pane fade {{ (  isset($params['ctabval'])  && $params['ctabval'] == 'careeronejobs' ) ? 'active in' : '' }}">
    @include('site.partials.careeronejobs', array('careeronejobs' => $careeronejobs))
    @include('site.partials.paginator', array('careeronejobs' => $careeronejobs))
  </div>
</div>
<script>
  $(document).on('shown.bs.tab','a[data-toggle="tab"]', function(e){
    $('#ctabval').val($(this).attr('data-val'));
  });
</script>
<script type="text/javascript" src="https://cdn.careeronecdn.com.au/lux/careerone/third_party/typeahead.min.js"></script>
<Script>
  $('#search_location').typeahead(
    {
      highlight: true,
      minLength: 3,
    },
    {
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
    },
    {
      source: function (query, syncResults, asyncResults) {
        $.get('/site/search/location/autocomplete', {
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
    }
  );
  $('#search_location').on('change, typeahead:selected', function(event) {
    $('#location-prefill').hide();
  });
</Script>