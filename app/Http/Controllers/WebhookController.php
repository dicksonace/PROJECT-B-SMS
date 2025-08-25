<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Validator;
use App\Jobs\ProcessDlr;

class WebhookController extends Controller
{
    // public function dlr(Request $request)
    // {
    //     // 1. Verify signature
    //     $signature = $request->header('X-Signature');
    //     $secret = env('WEBHOOK_SECRET');
    //     $payload = [
    //         "sender_id" => $request->input('sender_id'),
    //         "receiver" => $request->input('receiver'),
    //         "content" => $request->input('content'),
    //         "idempotency_key" => $request->input('idempotency_key'),
        
    //     ];

    //     $payload = json_encode($payload);
    //     $computedSignature = hash_hmac('sha256', $payload, $secret);

    //     if (!$signature || !hash_equals($computedSignature, $signature)) {
    //         return response()->json(['error' => 'Invalid signature'], 400);
    //     }

    //     // 2. Validate payload
    //     $validator = Validator::make($request->all(), [
    //         'idempotency_key' => 'required|string|exists:messages,idempotency_key',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     $validated = $validator->validated();

    //     // 3. Find the message
    //     $message = Message::where('idempotency_key', $validated['idempotency_key'])->first();

    //     if (!$message) {
    //         return response()->json(['error' => 'Message not found'], 404);
    //     }

      
    //     $message->processed = true;
    //     $message->delivered_at = now();
        
    //     if ($message->status == 'sent'){
    //         $message->status = 'delivered';
    //     }else{
    //         $message->status = 'failed';
    //     }

    //     $message->save();

    //     return response()->json([
    //         'message' => 'DLR processed successfully',
    //         'data' => $message
    //     ], 200);
    // }

    

public function dlr(Request $request)
{
    // Signature verification (same as before)
    $signature = $request->header('X-Signature');
    $secret = env('WEBHOOK_SECRET');
    $payload = json_encode([
        "sender_id" => $request->input('sender_id'),
        "receiver" => $request->input('receiver'),
        "content" => $request->input('content'),
        "idempotency_key" => $request->input('idempotency_key'),
    ]);
    $computedSignature = hash_hmac('sha256', $payload, $secret);
    if (!$signature || !hash_equals($computedSignature, $signature)) {
        return response()->json(['error' => 'Invalid signature'], 400);
    }

    // Dispatch DLR processing to queue
    ProcessDlr::dispatch($request->all());

    // Respond immediately
    return response()->json(['message' => 'DLR received'], 200);
}

}
