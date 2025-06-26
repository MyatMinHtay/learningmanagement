<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>LMS</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

     <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link href="/assets/img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{asset('/assets/lib/animate/animate.min.css')}}" rel="stylesheet">
    <link href="{{asset('/assets/lib/owlcarousel/assets/owl.carousel.min.css')}}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{asset('/assets/css/bootstrap.min.css')}}" rel="stylesheet">

       {{-- jquery ui css 1 js1  --}}
    <link href="{{ asset('./assets/css/jquery-ui.min.css') }}" rel="stylesheet"  type="text/css">

    <!-- Custom Stylesheet -->
    <link href="{{asset('/assets/css/style.css')}}" rel="stylesheet">
</head>

<body>

 
    <x-header></x-header>
   {{$slot}}
   <x-footer></x-footer>

  
    <!-- JavaScript Libraries -->
    <script src="{{asset('/assets/js/jquery.min.js')}}"></script>
     <!-- bootstrap css1 js1  -->
    <script src="{{ asset('/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
    <script src="{{asset('/assets/lib/wow/wow.min.js')}}"></script>
    <script src="{{asset('/assets/lib/easing/easing.min.js')}}"></script>
    <script src="{{asset('/assets/lib/waypoints/waypoints.min.js')}}"></script>
    <script src="{{asset('/assets/lib/owlcarousel/owl.carousel.min.js')}}"></script>
    {{-- jquery ui css 1 js1 --}}
    <script src="{{ asset('/assets/js/jquery-ui.min.js') }}" type="text/javascript"></script>
    <!-- Custom Javascript -->
    <script src="{{asset('/assets/js/main.js')}}"></script>
</body>

</html>
