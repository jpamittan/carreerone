<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>INS Dashboard</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

@include('common.google.tagmanager.header')

<!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="{{ asset("/admin/bootstrap/css/bootstrap.min.css") }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset("/admin/dist/css/AdminLTE.min.css") }}">
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
          page. However, you can choose any other skin. Make sure you
          apply the skin class to the body tag so the changes take effect.
    -->
    <link rel="stylesheet" href="{{ asset("/admin/dist/css/skins/skin-blue.min.css") }}">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<body class="hold-transition skin-blue sidebar-mini">
@include('common.google.tagmanager.body')


@include('admin.layouts.header')
@include('admin.layouts.sidebar')
@yield('content')
<!--  @include('admin.layouts.footer') -->


<script src="{{ asset('/admin/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
<!-- Bootstrap 3.3.6 -->
<script src="{{ asset('/admin/bootstrap/js/bootstrap.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('/admin/dist/js/app.min.js') }}"></script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->

<script>
    $('.links').click(function (e) {

        uri = this.hash.substr(1);
        e.preventDefault();
        if (uri != '') {
            $('.content').html('');
            $.ajax({
                url: "<?php echo url('admin/users'); ?>",
                type: "GET",
                success: function (data) {
                    $('.content').html(data);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    //what to do in error
                },
                timeout: 15000//timeout of the ajax call
            });
        } else {
// No hash found
        }

//    

    });
</script>
<script>
    $('.roles').click(function (e) {

        uri = this.hash.substr(1);
        e.preventDefault();
        if (uri != '') {
            $('.content').html('');
            $.ajax({
                url: "<?php echo url('admin/permissiondetails'); ?>",
                type: "GET",
                success: function (data) {
                    $('.content').html(data);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    //what to do in error
                },
                timeout: 15000//timeout of the ajax call
            });
        } else {
// No hash found
        }

//    

    });
</script>
</body>
</html>