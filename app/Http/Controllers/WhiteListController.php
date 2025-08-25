<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Whitelist;
use Illuminate\Support\Facades\Validator;

class WhiteListController extends Controller
{
    public function index()
    {
        $whitelists = Whitelist::all();
        return response()->json($whitelists);
    }

    public function store(Request $request)
    {
        // Custom validator
        $validator = Validator::make($request->all(), [
            'sender_id' => 'required|string|unique:whitelists,sender_id',
        ], [
            'sender_id.unique' => 'This Sender ID already exists in the whitelist.',
        ]);

        // If validation fails, return custom message
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 409); 
        }

        // Save to database
        $whitelist = Whitelist::create([
            'sender_id' => $request->input('sender_id')
        ]);

        return response()->json([
            'message' => 'Sender ID added to whitelist successfully',
            'data' => $whitelist
        ], 201);
    }
}
