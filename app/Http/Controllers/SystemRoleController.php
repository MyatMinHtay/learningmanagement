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

    public function createrole(){
          $formData = request()->validate([
            
                'role' => ['required', 'string', 'regex:/^[a-z]+$/'],
                'description' => ['nullable','string','max:255'],
                'permissions.*' => ['required','string']
          ]);

          try{
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

    public function updaterole(SystemRole $role, Request $request) {
        $validatedData = $request->validate([
            'role' => 'required',
            'description' => 'nullable',
            'permissions' => 'array',
            'permissionsString' => 'nullable'
        ]);


    
        try{

            if($role->role == "adminstrator"){
                unset($validatedData['permissions']);
                $validatedData['permissions'] = $validatedData['permissionsString'];
                
            }else{
                $validatedData['permissions'] = implode(",",$validatedData['permissions']);
            }
           
            $role->update($validatedData);

            return redirect()->route('roles')->with('success','Role Update Successfully');
        }catch(QueryException $e){
            return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
        }
        
    
        // Redirect to a relevant page or return a response
    }

    public function deleterole(SystemRole $role){


        if($role->role == "adminstrator"){
            return redirect()->back()->with('warning',"You can't delete this record");
        }else{
            if(auth()->user()->role->role == "adminstrator" || auth()->user()->role->role == "admin"){

                if(auth()->user()->role->role == "admin" && $role->role == "admin"){
                    return redirect()->back()->with('warning',"You don't have permissions to delete");
                }else{
                    try{
                        $role->delete();
                        return redirect()->route('roles')->with('success',"Role Delete Successfully");
                }catch(QueryException $e){
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