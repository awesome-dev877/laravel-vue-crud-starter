<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Location;
use App\Http\Controllers\API\V1\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends BaseController
{

    protected $location = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $locations = Location::where('user_id', auth()->user()->id)->latest()->paginate(10);

        return $this->sendResponse($locations, 'Location list');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'address' => 'required|max:255',
            'city' => 'required|max:50',
            'state' => 'required|max:50',
            'zip' => 'required|max:20',
        ]);

        if ($validated->fails()) {
            return $this->sendError('Location Validation Failed', $validated->errors());
        }

        $location = new Location();
        $location->address = $request->get('address');
        $location->city = $request->get('city');
        $location->state = $request->get('state');
        $location->zip = $request->get('zip');
        $location->user_id = auth()->user()->id;
        $location->save();

        return $this->sendResponse($location, 'Location Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $location = Location::findOrFail($id);

        return $this->sendResponse($location, 'Location Details');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = Validator::make($request->all(), [
            'address' => 'required|max:255',
            'city' => 'required|max:50',
            'state' => 'required|max:50',
            'zip' => 'required|max:20',
        ]);

        if ($validated->fails()) {
            return $this->sendError('Location Validation Failed', $validated->errors());
        }

        $location = Location::findOrFail($id);

        $location->update($request->all());

        return $this->sendResponse($location, 'Location Information has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $location = Location::findOrFail($id);

        $location->delete();

        return $this->sendResponse($location, 'Location has been Deleted');
    }
}
