<?php

namespace App\Http\Controllers;

use App\Models\SavedVilla;
use Illuminate\Http\Request;

class SavedVillaController extends Controller
{
    //
    public function destroy(string $id){
       
        $villa = SavedVilla::find($id);

        if (!$villa) {
            
            return response()->json(['message' => 'Record not found'], 404);
        }

        // Delete the record
        $villa->delete();

        // Return a success message or redirect to a different route
        return response()->json(['message' => 'Record deleted successfully']);
    
    }

}
