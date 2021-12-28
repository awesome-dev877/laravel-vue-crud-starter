<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Customer;
use App\Http\Controllers\API\V1\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends BaseController
{

    protected $customer = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Customer $customer)
    {
        $this->middleware('auth:api');
        $this->customer = $customer;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = $this->customer->latest()->paginate(10);

        return $this->sendResponse($customers, 'Customer list');
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
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email',
            'phone' => 'required|regex:/(01)[0-9]{9}/',
        ]);

        if ($validated->fails()) {
            return $this->sendError('Customer Validation Failed', $validated->errors());
        }
        
        $customer = $this->customer->create([
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
            'email' => $request->get('email'),
            'phone' => $request->get('phone'),
        ]);

        return $this->sendResponse($customer, 'Customer Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = $this->customer->findOrFail($id);

        return $this->sendResponse($customer, 'Customer Details');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $customer = $this->customer->findOrFail($id);

        $validated = Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email',
            'phone' => 'required|regex:/(01)[0-9]{9}/',
        ]);

        if ($validated->fails()) {
            return $this->sendError('Customer Validation Failed');
        }

        $customer->update($request->all());

        return $this->sendResponse($customer, 'Customer Information has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = $this->customer->findOrFail($id);

        $customer->delete();

        return $this->sendResponse($customer, 'Customer has been Deleted');
    }
}
