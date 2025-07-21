@props(['name'])

@php
    $errorBag = $errors ?? session('errors');
@endphp

@if ($errorBag && $errorBag->has($name))
    <div class="col-10 mx-auto alert alert-danger alert-dismissible fade show my-3" role="alert">
        {{ $errorBag->first($name) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
