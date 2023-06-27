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
        <p>We received a request to reset the password associated with this email address.</br>
            if you made this request, please follow instruction below.
        </p>    
        <p>Click on the link below to reset your password using our source server:</p>
        </br>
        <a href="{{$link}}">{{$link}}</a>

</body>
</html>