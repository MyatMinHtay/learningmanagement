<x-adminlayout>
     <div class="container">


          <h1 class="text-center bg-purple mt-3">System Roles</h1>

          <div class="col-12 d-flex justify-content-end my-3">
              
               <a  class="btn btn-primary" data-bs-target="#rolecreatemodal" data-bs-toggle="modal"><i class="fa-solid fa-plus mx-1"></i>Add Roles</a>
          </div>

          <x-showerror name="role"></x-showerror>
          <x-showerror name="description"></x-showerror>
          <x-showerror name="permissions"></x-showerror>

          <div class="table-responsive roletable p-3">
               <table class="table table-hover">
                    <thead>
                         <th scope="col">Id</th>
                         <th scope="col">Role</th>
                         <th scope="col">Description</th>
                         <th scope="col">Permissions</th>
                         <th scope="col">Edit</th>
                         <th scope="col">Delete</th>
                    </thead>
                    <tbody>
                         @forelse ($roles as $role)
                              <tr>
                                   <td scope="row">{{$role->id}}</td>
                                   <td scope="row">{{$role->role}}</td>
                                   <td scope="row">{{$role->description}}</td>
                                   <td scope="row" class="d-flex flex-wrap">
                                        @forelse ($role->permissions as $permission)
                                             <div class="rounded-pill p-1 px-2 m-1 rolepill text-center">{{$permission}}</div>
                                        @empty
                                             <span class="rounded-pill rolepill">no permission</span>
                                        @endforelse
                                   </td>
                                   <td scope="row"><a href="/admin/roles/edit/{{$role->role}}"  class="btn btn-warning">Edit</a></td>
                                   <td scope="row"><a href="/admin/roles/delete/{{$role->role}}" class="btn btn-danger">Delete</a></td>
                              </tr>
                              
                              
                         @empty
                              
                         @endforelse
                        
                    </tbody>
               </table>
          </div>
          
     </div>

     {{-- Create Modal  --}}
     <div class="modal fade" id="rolecreatemodal" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Add Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              <form action="/admin/roles/create" method="POST" >
               @csrf
              <div class="modal-body">
                    <div class="form-group">
                              <label for="rolename">Role Name</label>
                              <input type="text" class="form-control" name="role" id="rolename" required placeholder="role name">
                    </div>

                   <div class="form-group">
                         <label for="roledescription">Description</label>
                         <textarea name="description" id="roledescription" class="form-control" cols="10" rows="1"></textarea>
                    </div>

                    <div class="permissions my-2">
                         

                      
                         @forelse ($allPermissions as $permission)
                             <label for="{{$permission}}">{{$permission}}</label>
                             <input type="checkbox" name="permissions[]" @if (auth()->user()->role->role == "admin")
                                   @if($permission == "all" || $permission == "roles")
                                   disabled="disabled"
                                   @endif
                             @endif value="{{$permission}}" id="{{$permission}}">
                         @empty
                             <span class="rounded-pill bg-primary">no permission</span>
                         @endforelse
                     
                     
                                   
                               
                       
                          

                   </div>
                    
                
  
                         <x-error name="systemroles"></x-error>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Create Role</button>
              </div>
          </form>
            </div>
          </div>
     </div>

   
</x-adminlayout>

<script type="text/javascript">
     $('document').ready(function(){
               $(".permissions input" ).checkboxradio();
          });
</script>

