<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--[if !mso]><!-->
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!--<![endif]-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title></title>
        <!--[if (gte mso 9)|(IE)]>
        <style type="text/css">
            table {border-collapse: collapse;}
        </style>
        <![endif]-->
        <style>
            table {
            border-spacing: 0;
            font-family: sans-serif;
            color: #333333;
            }
            td {
            padding: 0;
            }
            img {
            border: 0;
            }
            .full-width-image img {
            width: 100%;
            max-width: 650px;
            height: auto;
            }
            .image-padding img {
            width: 100%;
            max-width: 610px;
            height: auto;
            }
            .two-column {
            text-align: center;
            }
            @media (min-width: 645px) {
            .equalHeight {
            height: 100%;
            }
            }
            .contents {
            width: 100%;
            }
            .two-column img.email-image {
            width: 100%;
            height: auto;
            }
        </style>
    </head>
    <body style="Margin: 0;padding: 0;min-width: 100%;background-color: #eceded;">
        <div style="width: 100%; table-layout: fixed; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">
            <div>
                <!--[if (gte mso 9)|(IE)]>
                <table width="600" align="center">
                    <tr>
                        <td>
                            <![endif]-->
                            <table align="left" style="max-width: 650px;background-color:#ffffff;margin-top:5px;    ">
                                <tr style="background-image: linear-gradient(to right, #A7006A 0px, #630158 100%)">
                                    <td><br></td>
                                </tr>
                                <tr style="border-bottom: 1px solid #E1E1E1; display: block; padding-bottom:10px;">
                                    <td><img style="margin-top:10px; margin-left: 20px;" src="{{Config::get('app.url')}}/site/img/logo.jpg" /></td>
                                </tr>
                                @yield('content')
                                <?php /* 
                                    <tr>
                                        <td class="one-column">
                                            <table width="100%">
                                                <tr>
                                                    <td class="inner contents" style="background-color:#ffffff; padding:20px; text-align: center;">
                                                        <p><img src="{{Config::get('app.url')}}/site/img/thumbs-up.jpg" /></p>
                                    <h2 style='color: #BE0071; font-weight: 800;'>Hi Vacancy Team,</h2>
                                    <p>
                                    The following applications have been received for the role of <b>{{$details[0]->job_title}}</b>
                                    </p>
                                    
                                    <h4 style='color: #BE0071; font-weight: 400; text-decoration: underline;'>{{$details[0]->job_title}}</h4>
                                    <p  >
                                    <h5>List Of Job Applicants</h5>
                                    @foreach($details as $det)
                                    <h5>{{$det->first_name}} {{$det->last_name}}</h5>
                                    @endforeach
                                    <h5>The Hiring Manager for this role can schedule interviews at the following link </h5>
                                    <h4><a href="{{Config::get('app.url')}}site/schedule_interview/{{$details[0]->jobid}}">Click here to schedule interview</a>
                                    
                                    </h4>
                                    <br>
                                    or copy url <br>{{Config::get('app.url')}}site/schedule_interview/<br>{{$details[0]->jobid}}
                                    </p>
                                    
                                                         
                                                    </td>
                                                </tr>
                                                
                                            </table>
                                        </td>
                                    </tr>
                                    
                                    */ ?>
                                @if(! isset($noCoaching))
                                <tr style="border-bottom: 1px solid #E1E1E1; display: block; padding-bottom:10px;">
                                    <td>
                                        </td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="one-column">
                                        <table width="100%">
                                            <tr>
                                                <td class="inner contents" style="padding:20px; background-color:#fff; color:#000;">
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                        <tbody>
                                                            <tr>
                                                                <td style="padding-bottom:10px;">
                                                                    <p style="float:left; font-size: 14px;">&copy; {{ date('Y') }} - Copyright INS Career Management Pty Ltd</p>
                                                                </td>
                                                                <td valign="top">
                                                                    <table width="120" border="0" align="right" cellpadding="0" cellspacing="0">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>
                                                                                    <a href="https://www.facebook.com/inscareermanagement" target="_blank">
                                                                                    <img src="{{Config::get('app.url')}}/site/img/facebook-logo.jpg" alt="" style="outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; width: auto; max-width: 100%; float: left; clear: both; display: block; border: none;" align="left"/>
                                                                                    </a>
                                                                                </td>
                                                                                <td>
                                                                                    <a href="https://www.linkedin.com/company/ins-career-management" target="_blank">
                                                                                    <img src="{{Config::get('app.url')}}/site/img/linkedin-logo.jpg" alt="" style="outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; width: auto; max-width: 100%; float: left; clear: both; display: block; border: none;" align="left"/>
                                                                                    </a>
                                                                                </td>
                                                                                <td>
                                                                                    <a href="https://www.youtube.com/channel/UCzgv13_0Mfs5wYx3hHT3fSw" target="_blank">
                                                                                    <img src="{{Config::get('app.url')}}/site/img/youtube-logo.jpg" alt="" style="outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; width: auto; max-width: 100%; float: left; clear: both; display: block; border: none;" align="left"/>
                                                                                    </a>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                </td>
                                </tr>
                            </table>
                            <!--[if (gte mso 9)|(IE)]>
                        </td>
                    </tr>
                </table>
                <![endif]-->
            </div>
        </div>
    </body>
</html>