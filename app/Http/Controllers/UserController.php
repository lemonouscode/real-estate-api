<?php

namespace App\Http\Controllers;

use App\Models\SavedVilla;
use App\Models\User;
use App\Models\Villa;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
       
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      
        $savedVilla = new SavedVilla();
        $savedVilla->user_id = $request->id; // Assign the user ID to the 'user_id' column

        // I need to get villa ID
        $villa = Villa::where('slug', $request->slug)->first();

        $savedVilla->villa_id = $villa->id;
        $savedVilla->save();

        return response()->json(['Villa Saved']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        // Get user with saved villas along with villa details
        $user = User::with('savedVillas.villa')->find($id);

        return response()->json(['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        // $user = User::with('savedVillas.villa')->find($id);

        // $user->delete();

        // return response()->json(['Deleted']);
    }
}
