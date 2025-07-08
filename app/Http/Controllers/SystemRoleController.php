<?php

namespace App\Http\Controllers;

use App\Models\SystemRole;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class SystemRoleController extends Controller
{
    public function index()
    {
        try {
            $roles = SystemRole::all();
           
            foreach ($roles as $role) {
                $rolearray = $role->toArray();

                $string = $rolearray['permissions'];

                $array = explode(",", $string);
                
                $rolearray['permissions'] = $array;

                $role->permissions = $array;
            }

            $adminstrator = SystemRole::where('role', 'adminstrator')->first();
            $allPermissions = explode(',', $adminstrator->permissions);
               
            return view('admin.roles.index',[
                "roles" => $roles,
                "allPermissions" => $allPermissions
            ]);

        } catch (Exception $e) {
            Log::error('Error in roles index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load roles. Please try again.');
        }
    }

    /**
     * Create a new system role with permissions
     * Validates role data, converts permissions array to string, creates role record
     */
    public function createrole(){
          $formData = request()->validate([
            
                'role' => ['required', 'string', 'regex:/^[a-z]+$/'],
                'description' => ['nullable','string','max:255'],
                'permissions.*' => ['required','string']
          ]);

          try{
                // Convert permissions array to comma-separated string for storage
                $formData['permissions'] = implode(",",$formData['permissions']);

                $newrole = SystemRole::create($formData);
                return redirect()->route('roles')->with('success','Genre Created Successfully');
          }catch(QueryException $e){
                return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
          }
    }

    public function editrole(SystemRole $role)
    {
        try {
            $string = $role->permissions;
        
            $role->permissionsString = $string;
            $array = explode(",", $string);

            $role->permissions = $array;
        
            $adminstrator = SystemRole::where('role', 'adminstrator')->first();
            $allPermissions = explode(',', $adminstrator->permissions);
        
            return view('admin.roles.edit', [
                'role' => $role,
                'allPermissions' => $allPermissions
            ]);

        } catch (Exception $e) {
            Log::error('Error in editrole: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load role edit form. Please try again.');
        }
    }

    /**
     * Update existing system role with special handling for administrator role
     * Handles permission updates differently for admin vs regular roles, prevents admin permission loss
     */
    public function updaterole(SystemRole $role, Request $request) {
        $validatedData = $request->validate([
            'role' => 'required',
            'description' => 'nullable',
            'permissions' => 'array',
            'permissionsString' => 'nullable'
        ]);


    
        try{
            // Special handling for administrator role to maintain all permissions
            if($role->role == "adminstrator"){
                unset($validatedData['permissions']);
                $validatedData['permissions'] = $validatedData['permissionsString'];
                
            }else{
                // Convert permissions array to comma-separated string for regular roles
                $validatedData['permissions'] = implode(",",$validatedData['permissions']);
            }
           
            $role->update($validatedData);

            return redirect()->route('roles')->with('success','Role Update Successfully');
        }catch(QueryException $e){
            return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
        }
        
    
        // Redirect to a relevant page or return a response
    }

    /**
     * Delete system role with permission validation and constraint checking
     * Prevents deletion of administrator role, validates user permissions, handles foreign key constraints
     */
    public function deleterole(SystemRole $role){

        // Prevent deletion of critical administrator role
        if($role->role == "adminstrator"){
            return redirect()->back()->with('warning',"You can't delete this record");
        }else{
            // Validate user has sufficient permissions to delete roles
            if(auth()->user()->role->role == "adminstrator" || auth()->user()->role->role == "admin"){

                // Prevent admin users from deleting other admin roles (self-protection)
                if(auth()->user()->role->role == "admin" && $role->role == "admin"){
                    return redirect()->back()->with('warning',"You don't have permissions to delete");
                }else{
                    try{
                        $role->delete();
                        return redirect()->route('roles')->with('success',"Role Delete Successfully");
                }catch(QueryException $e){
                    // Handle foreign key constraint violations (role still in use)
                    if ($e->errorInfo[1] == 1451) {
                        return back()->withErrors(['error' => 'Cannot delete this record because it is referenced by another table.']);
                    } else {
                        return back()->withErrors(['error' => $e->getMessage()]);
                    }
                }
                }
               
            }else{
                return redirect()->back()->with('warning',"You don't have permissions to delete");
            }
        }
        
           
    }
    
}