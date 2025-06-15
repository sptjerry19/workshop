<?php

namespace App\Jobs;

use App\Models\Contact;
use App\Mail\ContactMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendContactEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $contact;

    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
        Log::info('SendContactEmail job constructed', ['contact_id' => $contact->id]);
    }

    public function handle(): void
    {
        try {
            Log::info('Attempting to send contact email', [
                'contact_id' => $this->contact->id,
                'email' => $this->contact->email
            ]);

            Mail::to('duylinhvnu@gmail.com')->send(new ContactMail($this->contact));

            Log::info('Contact email sent successfully', [
                'contact_id' => $this->contact->id
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send contact email', [
                'contact_id' => $this->contact->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
