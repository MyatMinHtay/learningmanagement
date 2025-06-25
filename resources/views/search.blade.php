<x-layout>
     <div class="container">
        <form action="{{ route('search') }}" method="GET">
            <input type="text" name="q" placeholder="Search...">
            <button type="submit">Search</button>
        </form>

        <h2>Search Results for "{{ $query ?? '' }}"</h2>

        <h3>Courses</h3>
        <ul>
            @if ($courses->count() > 0)
                @foreach($courses as $course)
                    <li>{{ $course->name }}</li>
                @endforeach
            @endif
        </ul>




       


    </div>
</x-layout>