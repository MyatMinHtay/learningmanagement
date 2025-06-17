<x-adminlayout>
     <div class="container mt-5">
          <div class="row justify-content-center">
              <div class="col-md-8">
                  <div class="card">
                      <div class="card-header">Edit User</div>

                      <div class="img-container">

                            <img src="{{ asset($user->userphoto) }}" width="100" height="100" alt="">

                      </div>

                      <div class="card-body">
                          <form method="POST" action="{{ route('admin.users.update', $user->username) }}" enctype="multipart/form-data">
                              @csrf



                              <div class="form-group mb-3">
                                   <label for="username">Username</label>
                                   <input id="username" type="text" class="form-control inputbox" name="username" value="{{ old('username', $user->username) }}">
                                   <x-error name="username" />
                               </div>



                              <div class="form-group mb-3">
                                  <label for="email">Email</label>
                                  <input id="email" type="email" class="form-control inputbox" name="email" value="{{ old('email', $user->email) }}">
                                  <x-error name="email" />
                              </div>



                              <div class="form-group mb-3">
                                  <label for="password">Password</label>
                                  <input id="password" type="password" class="form-control inputbox" name="password">
                                  <x-error name="password" />
                              </div>

                              <div class="form-group mb-3">
                                   <label for="password_confirmation">Confirm Password</label>
                                   <input id="password_confirmation" type="password" class="form-control inputbox" name="password_confirmation">
                                   <x-error name="password_confirmation" />
                               </div>

                               



                              <div class="form-group mb-3">
                                  <label for="userphoto">User Photo</label>
                                  <input id="userphoto" type="file" class="form-control inputbox" name="userphoto">
                                  <x-error name="userphoto" />
                              </div>




                            <div class="form-group mb-3 permissions my-2">

                              @forelse ($systemroles as $systemrole)
                                  <label for="{{$systemrole->role}}">{{$systemrole->role}}</label>
                                  <input type="radio" name="role_id" @if ($systemrole->id === $user->role_id) checked @endif  value="{{$systemrole->id}}" id="{{$systemrole->role}}">
                              @empty
                                  <span class="rounded-pill bg-primary">no role</span>
                              @endforelse
                                <x-error name="role_id" />
                        </div>



                            <div class="col-12 d-flex justify-content-end align-items-center">
                                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-upload"></i></button>
                            <a href="/admin/users/lock/{{$user->username}}" class="btn btn-warning mx-1"><i class="fa-solid fa-lock"></i></a>
                                    <a href="/admin/users/unlock/{{$user->username}}" class="btn btn-success mx-1"><i class="fa-solid fa-lock-open"></i></a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-adminlayout>

<script type="text/javascript">
     $('document').ready(function(){
          $(".permissions input" ).checkboxradio();

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



