<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">
    <link href="{{URL::to('/site/img/favicon.ico')}}" type="image/x-icon" rel="shortcut icon">

    @include('common.google.tagmanager.header')

    <title>INS</title>
    <!-- font-family -->
    <!-- Lato Font -->
    <link href='https://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic'
          rel='stylesheet' type='text/css'/>
    <!-- End Lato Font -->
    <!-- end font-family-->
    <!-- custom css -->
    <link href="{{URL::to('/site/css/bootstrap.css')}}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{URL::to('/site/css/dateTimePicker.css')}}"/>
    <link href="{{URL::to('/site/css/style.css')}}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{URL::to('/site/css/calandar.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{URL::to('/site/css/jobview.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{URL::to('/site/css/dashboard.css')}}"/>
    <!-- end custom css-->
    <!-- Bootstrap core CSS -->
    <!-- Latest compiled and minified CSS -->
    <script src="https://code.jquery.com/jquery-2.1.4.js"></script>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        .ins {
            padding-top: 0px;
            background-color: white;
        }
    </style>

</head>
<body class="ins">
@include('common.google.tagmanager.body')

@yield('content')

<!-- Bootstrap core JavaScript
    ================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script> -->
<!--     <script type="text/javascript" src="{{URL::to('/site/js/jquery.js')}}"></script> -->


<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<!--   <script src="{{URL::to('/site/js/ie10-viewport-bug-workaround.js')}}"></script> -->

<script src="{{URL::to('/site/js/bootstrap.min.js')}}"></script>
<script type="text/javascript" src="{{URL::to('/site/js/dateTimePicker.min.js')}}"></script>
<!-- <script type="text/javascript" src="{{URL::to('/site/js/schedule-interview.js')}}"></script> -->


</body>
</html>                                		