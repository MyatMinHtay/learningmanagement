<x-adminlayout>
     <section class="home container-fluid">
          <div class="text my-3">Dashboard Sidebar</div>

          <div class="accountboxcontainer">
               <div class="accountbox">
                    
                    <div><i class="fa-solid fa-dollar-sign fs-5 icon"></i> Earning </div>
                    <div>{{$earning}}</div>
               </div>
               <div class="accountbox">
                    <div><i class="fa-solid fa-user fs-5 icon"></i> Users </div>
                    <div>{{$users}}</div>
                    
               </div>
               <div class="accountbox">
                    <div><i class="fa-solid fa-medal fs-5 icon"></i> Orders </div>
                    <div>{{$orders}}</div>
               </div>
               <div class="accountbox">
                    <div><i class="fa-solid fa-book fs-5 icon"></i> Novels </div>
                    <div>{{$novels}}</div>
               </div>
          </div>
      </section>
</x-adminlayout>