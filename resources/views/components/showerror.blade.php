@props(['name'])

@error($name)

     <div class="col-10 mx-auto alert alert-danger alert-dismissible fade show my-3" role="alert">
       {{$message}}
     <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
   </div>
@enderror