<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Whitelist;
use Illuminate\Support\Facades\Validator;
use App\Jobs\ProcessMessage;

class MessageController extends Controller
{


   public function index(Request $request)
    {
 
        $perPage = $request->query('per_page', 10);

        $messages = Message::orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($messages);
    }


   public function store(Request $request)
   {
    $validator = Validator::make($request->all(), [
        'sender_id' => 'required|string',
        'to' => 'required|string',
        'content' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $validated = $validator->validated();

    // Get Idempotency-Key from header
    $idempotencyKey = $request->header('Idempotency-Key');

    if (!$idempotencyKey) {
        return response()->json(['error' => 'Idempotency-Key header is required'], 400);
    }

    // Check duplicate
    if (Message::where('idempotency_key', $idempotencyKey)->exists()) {
        return response()->json(['message' => 'This message has already been sent.'], 409);
    }

    // Check whitelist
    if (!Whitelist::where('sender_id', $validated['sender_id'])->exists()) {
        return response()->json(['error' => 'Sender not whitelisted'], 403);
    }

    // Create message
    $message = Message::create([
        'sender_id' => $validated['sender_id'],
        'receiver' => $validated['to'],
        'content' => $validated['content'],
        'idempotency_key' => $idempotencyKey,
        'processed' => false,
        'status' => 'pending',
    ]);
    ProcessMessage::dispatch($message);

    // Build a custom payload with only the fields you want
        $payloadArray = [
        'sender_id' => $validated['sender_id'],
        'receiver' => $validated['to'],
        'content' => $validated['content'],
        'idempotency_key' => $idempotencyKey,
        ];

        $payload = json_encode($payloadArray, JSON_UNESCAPED_SLASHES);

        // Generate signature
        $secret = env("WEBHOOK_SECRET");
        $xSignature = hash_hmac('sha256', $payload, $secret);

    return response()->json([
        'message' => 'Message received',
        'data' => $message,
        'payload' => $payloadArray,
        'x_signature' => $xSignature,

    ], 201);
}


  function show($id){
    $message = Message::find($id);
    if (!$message) {
        return response()->json(['error' => 'Message not found'], 404);
    }
    return response()->json($message);
  }

}
