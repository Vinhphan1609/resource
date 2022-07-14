
<!DOCTYPE html>
<html lang="zxx">
<head>
    <!-- Google Tag Manager -->

    <title>@yield('pageTitle') - Chainos Solution </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <!-- External CSS libraries -->
    <link rel="stylesheet" href="{{asset('assets/css/vendor/bootstrap/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/vendor/bootstrap-checkbox.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/vendor/bootstrap/bootstrap-dropdown-multilevel.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/minimal.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/custom.css')}}">
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">

</head>

<body class="bg-1">
    <!-- Wrap all page content here -->
    <div id="wrap">
        <!-- Make page fluid -->
        <div class="row">
            @yield('content')
        </div>
    </div>
    <!-- Wrap all page content end -->
</body>
<!-- jQuery -->
<script src="{{asset('assets/js/vendor/jquery/dist/jquery.min.js')}}"></script>
<script>
    if ($(".alert")[0]){
        setTimeout(function(){ $(".alert").fadeOut() }, 3000);
    }
</script>
</html>