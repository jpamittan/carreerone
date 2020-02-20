
<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="index2.html" class="logo"><b>Dashboard</b></a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Navbar Right Menu -->
        @if(Auth::check())
        <div class="navbar-custom-menu" style="margin-top: 10px;margin-right: 37px;">
            <div class="pull-right">
                    <a href="{{URL::route('admin-logout')}}" class="btn btn-default btn-flat">Log Out</a>
            </div>
        </div>
        @endif
    </nav>
</header>
