<?php
    $dashboard = '';
    $profile = '';
    $calendar = '';
    $resources = '';

    $croute =  Route::getCurrentRoute()->getPath();;
    switch( $croute){
        case 'site/dashboard':  $dashboard = 'activebottom'; ; break;
        case 'site/profile':  $profile = 'activebottom'; ; break;
        case 'site/interview':  $calendar = 'activebottom'; ; break;
        case 'site/resources':  $resources = 'activebottom'; ; break;
        default : $dashboard = 'activebottom';
    }


?>
<div class="navbar-wrapper container">
        <!-- Fixed navbar -->
        <nav class="navbar navbar-default navbar-fixed-top insNav1">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="{{URL::route('site.home.dashboard') }}"><img class="ins_logo" src="/site/img/ins-logo.png" alt="logo">
                    </a>
                </div>
                <div class="mobile_menu header_row">
                        <div class="nav_menu">
                            <div class="top">
                                <i aria-hidden="true" class="c1-basics-left"></i>
                            </div>
                            <div id="header_bottom">
                                <div id="header_nav">
                                    <ul id="header_nav_inner">
                                        <li><a href="{{URL::route('site.home.dashboard') }}" class="c1-track"><img class="navimg img1" src="/site/img/dashboard.jpg" />&nbsp;&nbsp;Dashboard</a></li>
                                        <li><a href="{{URL::route('site.home.profile') }}" class="c1-track"><img class="navimg img2" src="/site/img/user.jpg" />&nbsp;&nbsp;Profile</a></li>
                                        <li><a href="{{URL::route('confirm-interview') }}" class="c1-track"><img class="navimg img3" src="/site/img/calender.jpg" />&nbsp;&nbsp;Calandar</a></li>
                                        <li><a href="{{URL::route('site-resources') }}" class="c1-track"><img class="navimg img3" src="/site/img/calender.jpg" />&nbsp;&nbsp;Resources</a></li>

                                        @if(Auth::check())<li class="c1-track"><a href="{{URL::route('site-logout')}}" href="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Log Out</a></li>@endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav tabmenu-marg">
                        <li class="active">
                            <a href="{{URL::route('site.home.dashboard') }}"><img class="navimg img1" src="/site/img/dashboard.jpg" />&nbsp;&nbsp;Dashboard</a>
                            <div class="{{$dashboard}}"></div>
                        </li>
                        <li>
                            <a href="{{URL::route('site.home.profile') }}"><img class="navimg img2" src="/site/img/user.jpg" />&nbsp;&nbsp;Profile</a>
                            <div class="{{$profile}}"></div>
                        </li>
                        <li>
                            <a href="{{URL::route('confirm-interview') }}"><img class="navimg img3" src="/site/img/calender.jpg" />&nbsp;&nbsp;Calendar</a>
                            <div class="{{$calendar}}"></div>
                        </li>
                        <li>
                            <a href="{{URL::route('site-resources') }}"><img class="navimg img3" src="/site/img/calender.jpg" />&nbsp;&nbsp;Resources</a>
                            <div class="{{$resources}}"></div>
                        </li>
                         @if(Auth::check())
                            <li><a href="{{URL::route('site-logout')}}">Log Out</a></li>
                        @endif
                    </ul>

                </div>
                <!--/.nav-collapse -->
            </div>
        </nav>

    </div>
