<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    {{-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> --}}

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Learning Management</title>

    <!-- favicon  -->
    <link href="{{ asset('./assets/img/logo1.jpg') }}" width="16" rel="icon" type="image/jpg">

   <!-- fontawesome  -->
   <link href="{{ asset('./assets/fontawesomefree/css/all.min.css') }}" rel="stylesheet" type="text/css">

    {{-- jquery ui css 1 js1  --}}
    <link href="{{ asset('./assets/css/jquery-ui.min.css') }}" rel="stylesheet"  type="text/css">

      <link href="{{ asset('/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

      {{-- Summer note CSS1 JS 1  --}}
      <link href="{{ asset('./assets/summernote/summernote-bs5.min.css') }}" rel="stylesheet" type="text/css">
    <!-- custom css -->
    <link rel="stylesheet" href="{{ asset('./assets/css/admin.css') }}" type="text/css">
    {{-- <link rel="stylesheet" href="{{ asset('./assets/css/style.css') }}" type="text/css"> --}}





    <body id="nightbody">



     <x-adminnavbar></x-adminnavbar>
     {{$slot}}





    <!-- jquery js1  -->
    <script src="{{ asset('./assets/js/jquery.min.js') }}" type="text/javascript"></script>

    {{-- jquery ui css 1 js1 --}}

    <script src="{{ asset('/assets/js/jquery-ui.min.js') }}" type="text/javascript"></script>

    {{-- fontawesome  --}}
    <script src="{{ asset('./assets/fontawesomefree/js/all.min.js') }}"></script>

    <!-- bootstrap css1 js1  -->
    <script src="{{ asset('/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>

    {{-- summer note js 1  --}}
    <script src="{{ asset('./assets/summernote/summernote-bs5.min.js') }}" type="text/javascript"></script>
</body>
</html>
