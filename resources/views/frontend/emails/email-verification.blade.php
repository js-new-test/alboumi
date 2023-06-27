<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <!-- <title>Admin Dashboard</title> -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no"/>
    <meta name="description" content="This is an example dashboard created using build-in elements and components.">

    <!-- Disable tap highlight on IE -->
    <meta name="msapplication-tap-highlight" content="no">

    @include('admin.include.top')
</head>
<body>                              
        <h2>We're excited to have you get started. First, you need to confirm your account. Just press the button below.</h2>        
        <div class="col-lg-12">
            <!-- <div class="main-card mb-3 card"> -->
                <!-- <div class="card-body">                    -->
                    <div class="text-center">
                        <a href="{{$link}}"><button class="btn-wide mb-2 mr-2 btn-square btn btn-primary btn-lg">Verify Email</button></a>                        
                    </div>
                <!-- </div> -->
            <!-- </div>             -->
        </div>          
        <p>If that doesn't work, copy and paste url in your browser to verify your email.</p>    
        <a href="{{$link}}">{{$link}}</a>
</body>
</html>