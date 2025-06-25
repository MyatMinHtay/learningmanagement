<x-adminlayout>
     <div class="container">
          <h1 class="text-center bg-purple">Genres</h1>

          <x-showerror name="genre_name"></x-showerror>
          <x-showerror name="genre_slug"></x-showerror>

          <div class="col-12 d-flex justify-content-end my-3">
              
               <a  class="btn btn-green" data-bs-target="#genrecreatemodal" data-bs-toggle="modal"><i class="fa-solid fa-plus mx-1"></i>Add Genre</a>
          </div>

          <div class="table-responsive">
               <table class="table table-hover table-primary">
                    <thead>
                         <th>Id</th>
                         <th>Genre Name</th>
                         <th>Genre Slug</th>
                         <th>Edit</th>
                         <th>Delete</th>
                    </thead>
                    <tbody>
                         @forelse ($genres as $genre)
                              <tr>
                                   <td>{{$genre->id}}</td>
                                   <td>{{$genre->genre_name}}</td>
                                   <td>{{$genre->genre_slug}}</td>
                                   <td><a  class="btn btn-warning editgenre"><i class="fa-solid fa-pen-to-square"></i></a></td>
                                   <td><a href="/admin/genres/delete/{{$genre->genre_slug}}" class="btn btn-danger"><i class="fa-solid fa-trash"></i></a></td>
                              </tr>
                         @empty
                              
                         @endforelse
                        
                    </tbody>
               </table>
          </div>
          
     </div>

     {{-- Create Modal  --}}
     <div class="modal fade" id="genrecreatemodal" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content bg-color">
              <div class="modal-header">
                <h5 class="modal-title">Add Genre</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              <form action="/admin/genres/create" method="POST" >
               @csrf
              <div class="modal-body">
                    <div class="form-group my-1">
                              <label for="genrename">Genre Name</label>
                              <input type="text" class="form-control inputbox" name="genre_name" id="genrename" required placeholder="genre name">
                    </div>

                   <div class="form-group my-1">
                         <label for="genreslug">Genre Slug</label>
                         <input type="text" class="form-control inputbox" id="genreslug" name="genre_slug" required placeholder="genre slug">
                    </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-green">Create Genre</button>
              </div>
          </form>
            </div>
          </div>
     </div>

     {{-- Edit Modal  --}}
     <div class="modal fade" id="genreeditmodal" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content bg-color">
              <div class="modal-header">
                <h5 class="modal-title">Edit Genre</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              <form  id="genreeditform" method="POST" >
               @csrf
               <div class="modal-body">
                         <div class="form-group">
                                   <label for="genrename">Genre Name</label>
                                   <input type="text" class="form-control inputbox" name="genre_name" id="genrenameedit" required placeholder="genre name">
                         </div>

                    <div class="form-group">
                              <label for="genreslug">Genre Slug</label>
                              <input type="text" class="form-control inputbox" id="genreslugedit" name="genre_slug" required placeholder="genre slug">
                         </div>
               </div>
               <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-green">Update Genre</button>
               </div>
               </form>
            </div>
          </div>
     </div>
</x-adminlayout>

<script type="text/javascript">
        $('document').ready(function(){
               $(".editgenre").on('click',function(){
                   let genrerow =  $(this.closest('tr'));

                   let data = genrerow.children('td').map(function(){
                         return $(this).text()
                   });

                   let genreslug = data[2];
                   console.log(genreslug);
                   $("#genreeditform").attr("action","/admin/genres/update/" + genreslug);
               

                   console.log($("#genreeditform").attr('action'));
                   $("#genrenameedit").val(data[1]);
                   $('#genreslugedit').val(data[2]);

                   $('#genreeditmodal').modal('show');

               });
        });
</script>