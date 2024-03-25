<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $service = Service::all();
        return response()->json($service);
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
            'description' => 'required|string',
            'price' => 'required|numeric',
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()->toJson()]);
        }

        $service = Service::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price
        ]);

        return response()->json($service, 201);
    }

    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }
        return response()->json($service);
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'string',
            'price' => 'numeric',
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()->toJson()]);
        }

        $service->update([
            'name' => $request->name ?? $service->name,
            'description' => $request->description ?? $service->description,
            'price' => $request->price ?? $service->price,
        ]);

        return response()->json('Service is updated successfully');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        $service->delete();
        return response()->json(['message' => 'Service deleted successfully']);
    }

        public function services_count()
    {
        return response()->json(Service::count());
    }

    public function famous_services()
    {
        return response()->json(Service::OrderBy('subscribers', 'desc')->take(5)->get());
    }

        public function total_usage()
    {
        $totalUsage = UserService::sum('usage');

        return response()->json([
            'total_usage' => $totalUsage,
        ]);
    }
}
