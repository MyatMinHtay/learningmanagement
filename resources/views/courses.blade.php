<x-layout>




    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Courses</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="#">Home</a></li>
                            <li class="breadcrumb-item"><a class="text-white" href="#">Pages</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Courses</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->


    <!-- Categories Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Categories</h6>
                <h1 class="mb-5">Course Categories</h1>
            </div>
            
            <!-- Category Filter -->
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8">
                    <div class="text-center">
                        <form method="GET" action="{{ route('courses') }}" class="d-flex justify-content-center flex-wrap gap-2">
                            <a href="{{ route('courses') }}" class="btn {{ !$selectedCategory ? 'btn-primary' : 'btn-outline-primary' }} mx-1 mb-2">
                                All Courses
                            </a>
                            @foreach($categories as $category)
                                <a href="{{ route('courses', ['category' => $category->id]) }}" 
                                   class="btn {{ $selectedCategory == $category->id ? 'btn-primary' : 'btn-outline-primary' }} mx-1 mb-2"
                                   style="{{ $selectedCategory == $category->id ? 'background-color: ' . $category->color . '; border-color: ' . $category->color . ';' : 'color: ' . $category->color . '; border-color: ' . $category->color . ';' }}">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </form>
                    </div>
                </div>
            </div>
            
            @if($selectedCategory)
                @php
                    $selectedCategoryName = $categories->firstWhere('id', $selectedCategory)->name ?? 'Unknown';
                @endphp
                <div class="row">
                    <div class="col-12 text-center mb-4">
                        <div class="alert alert-info">
                            <i class="fa fa-filter"></i>
                            Showing courses in category: <strong>{{ $selectedCategoryName }}</strong>
                            <a href="{{ route('courses') }}" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="fa fa-times"></i> Clear Filter
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <!-- Categories End -->

    <!-- Courses Start -->
    <x-showcourses  :courses="$courses"></x-showcourses>
    <!-- Courses End -->


  

</x-layout>
        

    