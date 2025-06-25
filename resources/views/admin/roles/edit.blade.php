<x-adminlayout>
     <div class="container my-3">
         

          <form action="/admin/roles/update/{{$role->role}}" class="bg-color p-3" method="POST" >
               @csrf
               <h1>Edit role</h1>
                    <div class="form-group mb-3">
                              <label for="rolename">Role Name</label>
                              <input type="text" class="form-control inputbox" name="role" id="rolename" 
                              @if (old('role'))
                                   value="{{old('role')}}"
                              @else
                                   value="{{$role->role}}"
                              @endif required placeholder="role name">
                    </div>

                  
                    <div class="form-group mb-3">
                         <label for="roledescription">Description</label>
                         <textarea name="description" id="roledescription" class="form-control inputbox" cols="10" rows="1">@if(old('description')){{ old('description') }}@else{{$role->description}} @endif</textarea>
                     </div>
                     
                   
                    @if (auth()->user()->role->role == "adminstrator")
                    <div class="form-group mb-3">
                         <label for="rolename">Role Permissions</label>
                         <input type="text" class="form-control inputbox" name="permissionsString" id="permissions" value="{{old('permissions',$role->permissionsString)}}" placeholder="role permissions">
                    </div>
                    @endif
                    

                    <div class="permissions my-2">
                       
                         

                         @forelse ($allPermissions as $permission)
                              <label for="{{ $permission }}">{{ $permission }}</label>
                              <input type="checkbox" name="permissions[]" id="{{$permission}}" value="{{ $permission }}" {{ in_array($permission, $role->permissions) ? 'checked' : '' }} @if (auth()->user()->role->role == "admin" && $permission == "all")
                                   disabled="disabled"                          
                              @endif > 
                         @empty
                              <label for="user">user</label>
                              <input type="checkbox" name="permissions[]" id="user" value="user" {{ in_array($permission, $role->permissions) ? 'checked' : '' }} @if (auth()->user()->role->role == "admin" && $permission == "all")
                                   disabled="disabled"                          
                              @endif > 
                         @endforelse

                         
                   </div>
                    
                
  
                         <x-error name="permissions.*"></x-error>
               <div class="d-flex justify-content-end">
                         <button type="submit" class="btn btn-bg">Update Role</button>
               </div>
                         
          </form>
     </div>
</x-adminlayout>

<script type="text/javascript">
          $('document').ready(function(){
               $(".permissions input" ).checkboxradio();
          });
</script>