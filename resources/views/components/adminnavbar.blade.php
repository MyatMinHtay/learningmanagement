<div class="container-fluid">
     <nav class="navbar bg-body-tertiary sticky-top customnavbar mt-2">
          <div class="container-fluid customnavbarchild">

               <div class="navbar-toggler" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
                    aria-controls="offcanvasNavbar">
                    <i class="fa-solid fa-bars"></i>
               </div>


               <a class="navbar-brand fontcolor" href="/">Learning Management</a>

               <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar"
                    aria-labelledby="offcanvasNavbarLabel">
                    <div class="offcanvas-header">
                         <div class="userimgcontainer" style="background: url('{{ asset(auth()->user()->userphoto) }}'); background-position: center;
                         background-repeat: no-repeat;
                         background-size: cover;">
                              {{-- user photo --}}
                         </div>
                         <h5 class="offcanvas-title" id="offcanvasNavbarLabel">{{ auth()->user()->username }}
                              ({{ auth()->user()->role->role }}) </h5>

                         <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                              aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">


                         <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">

                              <li class="nav-item">
                                   <a class="nav-link" href="/admin/roles">
                                        <i class="fa-solid fa-r fs-5 icon"></i>
                                        Roles</a>
                              </li>

                              <li class="nav-item">
                                   <a class="nav-link" href="/admin/users">

                                        <i class="fa-solid fa-user fs-5 icon"></i>
                                        Users</a>
                              </li>

                              <li class="nav-item">
                                   <a class="nav-link" href="/logout">
                                        <i class="fa-solid fa-circle-chevron-left fs-5 icon"></i>
                                        Logout</a>
                              </li>






                         </ul>

                    </div>
               </div>
          </div>
     </nav>
</div>




@if (session('success'))
<x-alert type='success'>{{ session('success') }}</x-alert>
@endif

@if (session('warning'))
<x-alert type='warning'>{{ session('warning') }}</x-alert>
@endif

@if (session('danger'))
<x-alert type='danger'>{{ session('danger') }}</x-alert>
@endif
