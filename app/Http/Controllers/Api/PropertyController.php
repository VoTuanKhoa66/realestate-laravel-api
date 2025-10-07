<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return Property::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $data = $request->validate([
            'title' => 'required',
            'price' => 'required|numeric',
            'area' => 'required|integer',
            'address' => 'required',
            'description' => 'nullable',
        ]);
        return Property::create($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        //
        return $property;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Property $property)
    {
        //
        $property -> update($request -> all());
        return $property;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {
        //
        $property -> delete();
        return response('Delete property successfull !', 200);
    }
}
