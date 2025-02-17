<?php

namespace App\Jobs;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\ImportCompletedMail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImportContactsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $email;

    public function __construct($filePath, $email)
    {
        $this->filePath = $filePath;
        $this->email = $email;
    }

    public function handle()
    {
        try {
            $file = Storage::get($this->filePath);
            $xml = simplexml_load_string($file);

            foreach ($xml->contact as $contact) {
                Contact::create([
                    'name' => (string) $contact->name,
                    'phone' => (string) $contact->phone
                ]);
            }

            Mail::to($this->email)->send(new ImportCompletedMail());
        } catch (\Exception $e) {
            Log::error('Error importing contacts: ' . $e->getMessage());
        }
    }
}