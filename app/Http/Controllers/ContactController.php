<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;
use App\Models\Contact;

class ContactController extends Controller
{
    //
    public function submit(Request $request)
    {
        // Validate the incoming data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        // Send email using Mailgun
        Mail::to('testreceiver@gmail.com')->send(new ContactMail($validatedData));

        // Save contact form into db
        Contact::create($validatedData);

        return response()->json(['message' => 'Message sent successfully'], 200);
    }

}
