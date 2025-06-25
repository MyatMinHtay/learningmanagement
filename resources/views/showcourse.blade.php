<x-layout>
    <div class="container mt-5">
        <div class="course-header">
            <div class="course-img">
                <img src="{{ asset($course->image) }}" alt="">
            </div>

           
        </div>

        <h4 class="show-course-ttl">{{ $course->name }}</h4>

        <div id="accordion">
            @foreach ($course->modules as $index => $module)
                <h3>{{ $index + 1 }}. {{ $module->title }}</h3>
                <div>
                    <div class="module-content">
                        {!! nl2br(e($module->content)) !!}
                    </div>
                </div>
            @endforeach
        </div>
        


        <div class="desc-box my-5">
            <h5 class="desc-title">Description</h5>
            <p class="desc-text">
                {!! $course->description !!}
            </p>
        </div>
    </div>
</x-layout>

<script>
  $( function() {

     $( "#accordion" ).accordion({
      collapsible: true
    });
  } );
  </script>