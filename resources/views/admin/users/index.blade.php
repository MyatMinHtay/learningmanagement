<x-adminlayout>
     <div class="container">


          <h1 class="text-center bg-purple mt-3">Users</h1>

          <div class="col-12 d-flex justify-content-end my-3">
            
            <a  class="btn btn-primary mx-2" data-bs-target="#usercreatemodal" data-bs-toggle="modal"><i class="fa-solid fa-plus mx-1"></i>Add Users</a>
          </div>

        


          <div class="container-fluid my-3">
               <div class="row justify-content-center">
                    <div class="col-md-12">
                        
                             

                              
                              <form action="" method="GET">
                                   <div class="d-flex justify-content-between flex-wrap">
                                        <div class="form-group mr-2">
                                             <label for="name">Name</label>
                                             <input type="text" class="form-control" value="{{request('name')}}" id="name" name="name" placeholder="Enter name">
                                        </div>

                                        <div class="form-group mr-2">
                                             <label for="role">Role</label>
                                             <select class="form-control" id="role" name="role">
                                                  <option value="">Select Role</option>
                                                       @foreach ($systemroles as $systemrole)
                                                            <option value="{{ $systemrole->role }}" @if ($systemrole->role === $request->role)
                                                                 selected
                                                            @endif>{{ $systemrole->role }}</option>
                                                       @endforeach
                                             </select>
                                        </div>

                                        <div class="form-group mr-2">
                                             <label for="email">Email</label>
                                             <input type="email" class="form-control" value="{{request('email')}}" id="email" name="email" placeholder="Enter email">
                                        </div>

                                        <div class="form-group">
                                             <button type="submit" class="btn btn-primary mt-4">Search</button>
                                        </div>
                                   </div>
                              </form>
                          
                         
                    </div>
               </div>
          </div>

          

        <div class="col-12 d-flex my-3">
          <div class="btn-group" role="group" aria-label="Basic outlined example">
              <a href="/admin/users" class="btn menubtns{{ !request()->filled('role') ? ' btn-bg-2' : '' }}">All</a>
              @foreach ($systemroles as $systemrole)
                  <a href="/admin/users?role={{ $systemrole->role }}" class="btn menubtns{{ request('role') === $systemrole->role ? ' btn-bg-2' : '' }}">{{ $systemrole->role }}</a>
              @endforeach
          </div>
      </div>
      
        

          <x-error name="systemrole"></x-error>
          <x-error name="name"></x-error>
          <x-error name="username"></x-error>
          <x-error name="email"></x-error>
          <x-error name="password"></x-error>
          <x-error name="password_confirmation"></x-error>
          <x-error name="userimg"></x-error>

          <div class="table-responsive">
               <table class="table table-hover table-bordered border-1 table-primary">
                    <thead>
                         <th scope="col">Id</th>
                         <th scope="col">Role</th>
                         <th scope="col">Username</th>
                         <th scope="col">Email</th>
                         <th scope="col">Status</th>
                         <th scope="col">Edit</th>
                         
                         <th scope="col">Delete</th>
                    </thead>
                    <tbody>
                         @forelse ($users as $user)
                              <tr>
                                   <td scope="row">{{$user->id}}</td>
                                   <td scope="row">{{$user->role}}</td>
                                   <td scope="row">{{$user->username}}</td>
                                   <td scope="row">{{$user->email}}</td>
                                   

                                   <td scope="row">{{$user->status}}</td>
                                 
                                  
                                   <td scope="row" class="text-center"><a href="/admin/users/edit/{{$user->username}}"  class="btn btn-info"><i class="fa-solid fa-pen-to-square"></i></a></td>
                                   {{-- <td scope="row" class="text-center">

                                    <a href="/admin/users/lock/{{$user->username}}" class="btn btn-warning mx-1"><i class="fa-solid fa-lock"></i></a>
                                    <a href="/admin/users/unlock/{{$user->username}}" class="btn btn-success mx-1"><i class="fa-solid fa-lock-open"></i></a>
                                  </td> --}}
                                  
                                   <td scope="row" class="text-center"><a href="/admin/users/delete/{{$user->username}}" class="btn btn-danger"><i class="fa-solid fa-trash"></i></a></td>
                              </tr>
                              
                              
                         @empty
                               <tr>
                                     <td scope="row" colspan="9" class="text-center">There is no record</td>                                                                                                             
                               </tr>
                         @endforelse
                        
                    </tbody>
               </table>

                <div class="col-12 displayfixer mt-3 justify-content-end">
                    {{$users->links()}}
                </div>
          </div>
          
     </div>

    

     {{-- Create Modal  --}}
     <div class="modal fade" id="usercreatemodal">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
              
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

            
              <div class="modal-body">
               <div class="card p-4 my-3 shadow-sm loginformContainer d-flex">
                    <div class="row">
  
                   
                        <div class="col-12 col-md-6">
                            <form action="/admin/users/create" class="signupbox" method="POST" enctype="multipart/form-data">
                              @csrf
                              <h3 class="text-center fontcolor">Add User</h3>
                              
  
                                <div class="form-group mb-3">
                                  <label for="username">Username</label>
                                  <input 
                                  type="text" 
                                  class="form-control inputbox"
                                  value="{{old('username')}}"
                                  name="username" id="username" aria-describedby="emailHelp" placeholder="Enter Username"
                                  required>
                                  
                                  
                                 
                                </div>
                              <div class="form-group mb-3">
                                <label for="exampleInputEmail1">Email address</label>
                                <input 
                                type="email"
                                class="form-control inputbox"
                                  name="email"
                                  value="{{old('email')}}" 
                                  id="exampleInputEmail1" 
                                  aria-describedby="emailHelp" 
                                  placeholder="Enter email"
                                  required>
          
                                 
                              </div>
  
                              
  
                              <div class="form-group mb-3">
                                <label for="exampleInputPassword1">Password</label>
                                <input 
                                type="password" class="form-control inputbox" 
                                name="password"
                                required
                                
                                id="password" placeholder="Password">
                               
                              
                                
                              </div>
  
                              <div class="form-group mb-3">
                                <label for="password_confirmation">Confirm Password</label>
                                <input type="password" class="form-control inputbox" name="password_confirmation" placeholder="Confirm Password" id="password_confirmation" required>
  
                                
                              </div>
  
                              <div class="form-group mb-3">
                                <label for="userimg">Photo</label>
                                <input 
                                type="file" class="form-control inputbox" 
                                name="userimg" id="userimg" placeholder="User Photo">
                        
                              
                              </div>
  
                              
                              
                              <div class="form-group mb-3 permissions my-2">
                         

                      
                                   @forelse ($systemroles as $systemrole)
                                       <label for="{{$systemrole->role}}">{{$systemrole->role}}</label>
                                       <input type="radio" name="role_id"  value="{{$systemrole->id}}" id="{{$systemrole->role}}">
                                   @empty
                                       <span class="rounded-pill bg-primary">no role</span>
                                   @endforelse
                               
                               
                                             
                                 
                                 
                                    
          
                             </div>
                              
                          
            
                                  
                           
                        </div>
                        <div class="col-12 col-md-6 my-3 my-md-0">
                            <div>
                                <h1>For Username</h1>
                                <p class="text-danger" id="threecharacter">Username must be 3 character at least</p>
                                <p class="text-danger" id="uppercase">Username can be contain uppercase</p>
                                <p class="text-danger" id="lowercase">Username can contain lowercase</p>
                                <p class="text-danger" id="number">Username can contain number</p>
                            </div>
  
                            <div>
                              <h1>For Password</h1>
                                <p class="text-danger" id="passwordeightcharacter">Passowrd must be 8 character at least</p>
                                <p class="text-danger" id="passworduppercase">Passowrd can be contain uppercase</p>
                                <p class="text-danger" id="passwordlowercase">Passowrd can contain lowercase</p>
                                <p class="text-danger" id="passwordnumber">Passowrd can contain number</p>
                                <p class="text-danger" id="passwordspecialcharacter">Passowrd can contain Special Character From(@$!%*?&)</p>
                            </div>
                        </div>
                  </div>

                 
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" id="submitbtn" name="submitLogin" class="btn btn-success">Add  User</button>  
              </div>
          </form>
            </div>
          </div>
     </div>

    

     

    

    

   
</x-adminlayout>

<script type="text/javascript">
          $('document').ready(function(){
               $(".permissions input" ).checkboxradio();
               $(".starsbox").checkboxradio();
               $(".btnbox a").button();
          });

          $(".menubtns").on('click',function(){
                $(".menubtns").removeClass("active");
          });

          function validateInput(inputValue) {
         var containsUppercase = /[A-Z]/.test(inputValue);
         var containsLowercase = /[a-z]/.test(inputValue);
         var containsNumber = /\d/.test(inputValue);
         var hasMinimumLength = inputValue.length >= 3;
   
         if (containsUppercase) {
             $("#uppercase").removeClass("text-danger");
             $("#uppercase").addClass("text-success");
         }else{
             $("#uppercase").removeClass("text-success");
             $("#uppercase").addClass("text-danger");
         }
   
       if (containsLowercase) {
             $("#lowercase").removeClass("text-danger");
             $("#lowercase").addClass("text-success");
       }else{
           $("#lowercase").removeClass("text-success");
           $("#lowercase").addClass("text-danger");
       }
   
       if (containsNumber) {
           $("#number").removeClass("text-danger");
           $("#number").addClass("text-success");
       }else{
           $("#number").removeClass("text-success");
           $("#number").addClass("text-danger");
       }
   
       if (hasMinimumLength) {
             $("#threecharacter").removeClass('text-danger');
             $('#threecharacter').addClass('text-success');
       }else{
         $('#threecharacter').removeClass('text-success');
           $("#threecharacter").addClass('text-danger');
             
       }
     }
   
   $("#username").on('keyup',function(){
       $value = $("#username").val();
   
       validateInput($value);
   });
   
   function validatePassword(password) {
     let containsLowercase = /[a-z]/.test(password);
     let containsUppercase = /[A-Z]/.test(password);
     let containsSpecialChar = /[@$!%*?&]/.test(password);
     let containsNumber = /\d/.test(password);
     let hasMinimumLength = password.length >= 8;
     
       if(containsLowercase){
           $("#passwordlowercase").removeClass("text-danger");
           $("#passwordlowercase").addClass("text-success");
       }else{
           $("#passwordlowercase").removeClass("text-success");
           $("#passwordlowercase").addClass("text-danger");
       }
   
       if(containsUppercase){
           $("#passworduppercase").removeClass("text-danger");
           $("#passworduppercase").addClass("text-success");
       }else{
           $("#passworduppercase").removeClass("text-success");
           $("#passworduppercase").addClass("text-danger");
       }
   
       if(containsSpecialChar){
           $("#passwordspecialcharacter").removeClass("text-danger");
           $("#passwordspecialcharacter").addClass("text-success");
       }else{
           $("#passwordspecialcharacter").removeClass("text-success");
           $("#passwordspecialcharacter").addClass("text-danger");
       }
   
       if(containsNumber){
           $("#passwordnumber").removeClass("text-danger");
           $("#passwordnumber").addClass("text-success");
       }else{
           $("#passwordnumber").removeClass("text-success");
           $("#passwordnumber").addClass("text-danger");
       }
   
       if(hasMinimumLength){
           $("#passwordeightcharacter").removeClass("text-danger");
           $("#passwordeightcharacter").addClass("text-success");
       }else{
           $("#passwordeightcharacter").removeClass("text-success");
           $("#passwordeightcharacter").addClass("text-danger");
       }
   }
   
   $("#password").on('keyup',function(){
       $value = $("#password").val();
   
       validatePassword($value);
   });
</script>

