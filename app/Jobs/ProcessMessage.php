<?php

namespace App\Jobs;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $message;

    /**
     * Create a new job instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Example: throughput control - wait if necessary
        // sleep(1); // simple rate limiting placeholder

        // Encode message (GSM-7 / UCS-2)
        $encodedContent = $this->encodeMessage($this->message->content);

        // Optionally split into segments
        $segments = $this->splitMessage($encodedContent);

 
       foreach ($segments as $segment) {
            \Log::info("Sending to {$this->message->receiver}: {$segment}");
        }

        // Optionally mark as "sent" (final status will be updated by DLR)
        $this->message->status = 'sent';
        $this->message->save();
    }

    private function encodeMessage($content)
    {
        // Simple example: detect GSM-7 vs UCS-2
        return mb_detect_encoding($content, 'ASCII', true) ? $content : $content; // placeholder
    }

    private function splitMessage($content)
    {
        // Simple 160 char segment split
        return str_split($content, 160);
    }
}
