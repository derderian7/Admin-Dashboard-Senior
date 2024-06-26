<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('service')->get();
        foreach( $users as $user ) {
            if($user->service)
            {
            foreach($user->service as $service) {
                $service_name = Service::find($service->service_id);
                if($service_name){
                $service_name = $service_name->name;
                $service->service_name =  $service_name;
                }
            }
        }
        }
        return response()->json($users);
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', Rules\Password::defaults()],
            'location' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->toJson()]);
        }
        $path = 'assets/uploads/users/' . $request->image;
        if (File::exists($path)) {
            File::delete($path);
        }
        $file = $request->file('image');
        $ext = $file->getClientOriginalExtension();
        $filename = time() . '.' . $ext;
        try {
            $file->move('assets/uploads/users/', $filename);
        } catch (FileException $e) {
            dd($e);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'image' => $filename,
            'location' => $request->location
        ]);

        return response()->json($user, 201);
    }


    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */public function user_update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'password' => [Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->toJson()]);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->location = $request->location;
        if ($request->image) {
            $path = 'assets/uploads/users/' . $request->image;
            if (File::exists($path)) {
                File::delete($path);
            }
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            try {
                $file->move('assets/uploads/users/', $filename);
            } catch (FileException $e) {
                dd($e);
            }
        $user->image = $filename;
        }
        $user->save();

        return response()->json('user is updated successfully');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function users_count()
    {
        return response()->json(User::count());
    }
    public function admin_change_password(Request $request)
    {
        $user = Auth::user();
        if(Hash::check($request->old_password, $user->password)){
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'password' => 'confirmed|min:6'
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()->toJson()]);
        }
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json(['message' => 'Password changed Successfully']);
    }
    return  response()->json(['message'=>"Wrong Password"]);
    }

    public function user_services()
    {
        $user = Auth::user();

        $services = $user->service;

        $serviceData = $services->map(function ($service) {
            return [
                'service_name' => $service->service->name,
                'usage' => $service->usage,
                'expiry_date' => $service->expiry_date,
                'created_at' => $service->created_at
            ];
        });

        return response()->json($serviceData, 200);
    }

        public function topCountries()
    {
        $topCountries = User::select('location', DB::raw('count(*) as count'))
            ->groupBy('location')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();

        return response()->json($topCountries);
    }
    
}
