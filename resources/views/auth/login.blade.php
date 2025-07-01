<x-layout>


    <!-- Starter Section Section -->
    <section id="starter-section" class="starter-section section">




           <div class="container loginbox displayFixer py-3">
                <div class="col-12 col-sm-8 col-lg-5 mx-auto p-3 loginformContainer">
                        <h2 class="text-center">Login Form</h2>

                        <form action="{{ route('page.login') }}" class="loginForm" method="post">

                            @csrf
                            <div class="form-group mx-auto my-1">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" value="{{old('email')}}" name="email" class="form-control inputbox" autocomplete="off" required/>
                            </div>

                            <x-error name="email"></x-error>

                            <div class="form-group mx-auto my-1">
                                    <label for="password">Password</label>
                                    <input type="password" id="password" name="password" value="{{old('passwords')}}" class="form-control inputbox" autocomplete="off" required/>
                                   <div id="password-error" class="text-danger mt-3" style="font-family: var(--default-font);"></div>
                            </div>

                            <x-error name="password"></x-error>


                            <div class="form-check col-12 me-auto my-1 d-flex justify-content-between">
                                <div>
                                  <input class="form-check-input" type="checkbox" value="1" name="remember" class=" "  id="flexCheckChecked" checked>
                                <label class="form-check-label" for="flexCheckChecked">
                                Remember me
                                </label>
                                </div>

                                <div class="d-flex align-items-center">
                                  <p class="my-0 me-2">Don't have an account?</p>
                                  <a href="{{ route('register') }}" class="text-decoration-none">Register</a>
                                </div>

                            </div>


                            <div class="d-grid col-6 mx-auto mt-3 loginbtn">

                                    <button type="submit" id="submitbtn" name="submitLogin" class="btn rounded-pill loginBtn">Login</button>

                            </div>




                        </form>

                </div>


            </div>


    </section><!-- /Starter Section Section -->



</x-layout>

<script>

const passwordInput = document.getElementById('password');
const passwordError = document.getElementById('password-error');


passwordInput.addEventListener('input', () => {
  const password = passwordInput.value;
  const errors = [];
  if (password.length < 8) {
    errors.push('Password must be at least 8 characters long');
  }
  if (!/[a-z]/.test(password)) {
    errors.push('Password must contain at least one lowercase letter');
  }
  if (!/[A-Z]/.test(password)) {
    errors.push('Password must contain at least one uppercase letter');
  }
  if (!/\d/.test(password)) {
    errors.push('Password must contain at least one number');
  }
  if (errors.length > 0) {
    passwordError.innerText = errors.join('\n');
  } else {
    passwordError.innerText = '';
  }
});
</script>
