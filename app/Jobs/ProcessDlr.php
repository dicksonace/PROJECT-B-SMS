<?php

namespace App\Jobs;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDlr implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $messageData;

    public function __construct(array $messageData)
    {
        $this->messageData = $messageData;
    }

    public function handle()
    {
        $message = Message::where('idempotency_key', $this->messageData['idempotency_key'])->first();

        if (!$message) {
            \Log::warning("DLR: Message not found for idempotency_key {$this->messageData['idempotency_key']}");
            return;
        }

        $message->processed = true;
        $message->delivered_at = now();
        $message->status = $message->status === 'sent' ? 'delivered' : 'failed';
        $message->save();
    }
}
