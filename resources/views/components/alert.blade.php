@props(['type' => 'success'])

<div class="col-10 mx-auto alert alert-{{$type}} alert-dismissible fade show my-5 my-lg-3" role="alert">
    {{$slot}}
     <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>