<x-layout>

    <!-- Starter Section Section -->
    <section id="starter-section" class="starter-section section">

         <div class="container mt-2 mb-5">
        <div class="row">
            <div class="col-md-6 mx-auto">

                <div class="p-4 my-3 shadow-sm loginformContainer signupcontainer d-flex">



                      <div class="col-12 mx-auto">
                          <form action="{{route('postregister')}}" class="signupbox" method="POST" enctype="multipart/form-data">
                            @csrf
                            <h3 class="text-center fontcolor">Register Form</h3>


                              <div class="form-group mb-3">
                                <label for="username">Username</label>
                                <input
                                type="text"
                                class="form-control inputbox"
                                value="{{old('username')}}"
                                name="username" id="username" aria-describedby="emailHelp" value="{{ old('username') }}" placeholder="Enter Username"
                                required>


                                <x-error name="username"></x-error>
                              </div>

                              <div class="form-group mb-3">
                                <p class="text-danger showtext" id="threecharacter">Username must be 3 character at least</p>
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

                                <x-error name="email"></x-error>
                            </div>

                             {{-- <div class="form-group mb-3 permissions my-2">
                                <label for="role_id">Select Role</label>
                                <select name="role_id" id="role_id" class="form-control" required>
                                    <option value="">-- Choose a role --</option>
                                    @forelse ($systemroles as $systemrole)
                                        <option value="{{ $systemrole->id }}">{{ $systemrole->role }}</option>
                                    @empty
                                        <option disabled>No roles available</option>
                                    @endforelse
                                </select>
                                <x-error name="role_id"></x-error>
                            </div> --}}




                            <div class="form-group mb-3">
                              <label for="exampleInputPassword1">Password</label>
                              <input
                              type="password" class="form-control inputbox"
                              name="password"
                              required

                              id="password" placeholder="Password">


                              <x-error name="password"></x-error>
                            </div>

                            <div class="form-group mb-3">
                              <p class="text-danger showtext" id="passwordeightcharacter">Passowrd must be 8 character at least</p>
                              <p class="text-danger showtext" id="passworduppercase">Passowrd must contain uppercase</p>
                              <p class="text-danger showtext" id="passwordlowercase">Passowrd must contain lowercase</p>
                              <p class="text-danger showtext" id="passwordnumber">Passowrd must contain number</p>
                              <p class="text-danger showtext" id="passwordspecialcharacter">Passowrd must contain Special Character From(@$!%*?&)</p>
                            </div>

                            <div class="form-group mb-3">
                              <label for="password_confirmation">Confirm Password</label>
                              <input type="password" class="form-control inputbox" name="password_confirmation" placeholder="Confirm Password" id="password_confirmation" required>

                              <x-error name="password_confirmation"></x-error>
                              @error('password_confirmation')
                                  <p>{{$message}}</p>
                              @enderror
                            </div>

                            <div class="form-group mb-3">
                              <label for="userphoto">Photo</label>
                              <input
                              type="file" class="form-control inputbox"
                              name="userphoto" id="userphoto" placeholder="User Photo">

                              <x-error name="userphoto"></x-error>
                            </div>


                            <div class="d-grid mt-5 mx-auto">
                              <p>Already have a account? <a href="/login" class="bgfontcolor">Login</a> </p>
                              <button type="submit" id="submitbtn" name="submitLogin" class="btn rounded-5 btn-success">Sign up</button>

                            </div>
                          </form>
                      </div>



                </div>



            </div>
        </div>
    </div>

    </section>



</x-layout>

<script>
    function validateInput(inputValue) {

        var hasMinimumLength = inputValue.length >= 3;


        if (hasMinimumLength) {
            $("#threecharacter").removeClass('text-danger d-block');
            $('#threecharacter').addClass('text-success d-none');
        } else {
            $('#threecharacter').removeClass('text-success d-none');
            $("#threecharacter").addClass('text-danger d-block');

        }
    }

    $("#username").on('keyup', function() {
        $value = $("#username").val();

        validateInput($value);
    });

    function validatePassword(password) {
        let containsLowercase = /[a-z]/.test(password);
        let containsUppercase = /[A-Z]/.test(password);
        let containsSpecialChar = /[@$!%*?&]/.test(password);
        let containsNumber = /\d/.test(password);
        let hasMinimumLength = password.length >= 8;

        if (containsLowercase) {
            $("#passwordlowercase").removeClass("text-danger");
            $("#passwordlowercase").addClass("text-success");
        } else {
            $("#passwordlowercase").removeClass("text-success");
            $("#passwordlowercase").addClass("text-danger");
        }

        if (containsUppercase) {
            $("#passworduppercase").removeClass("text-danger");
            $("#passworduppercase").addClass("text-success");
        } else {
            $("#passworduppercase").removeClass("text-success");
            $("#passworduppercase").addClass("text-danger");
        }

        if (containsSpecialChar) {
            $("#passwordspecialcharacter").removeClass("text-danger");
            $("#passwordspecialcharacter").addClass("text-success");
        } else {
            $("#passwordspecialcharacter").removeClass("text-success");
            $("#passwordspecialcharacter").addClass("text-danger");
        }

        if (containsNumber) {
            $("#passwordnumber").removeClass("text-danger");
            $("#passwordnumber").addClass("text-success");
        } else {
            $("#passwordnumber").removeClass("text-success");
            $("#passwordnumber").addClass("text-danger");
        }

        if (hasMinimumLength) {
            $("#passwordeightcharacter").removeClass("text-danger");
            $("#passwordeightcharacter").addClass("text-success");
        } else {
            $("#passwordeightcharacter").removeClass("text-success");
            $("#passwordeightcharacter").addClass("text-danger");
        }
    }

    $("#password").on('keyup', function() {
        $value = $("#password").val();

        validatePassword($value);
    });
</script>
