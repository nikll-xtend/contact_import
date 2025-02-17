<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ImportContactsJob;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function index()
    {
        try {
            $contacts = Contact::latest()->paginate(10);
            return view('contacts.index', compact('contacts'));
        } catch (\Exception $e) {
            Log::error('Error fetching contacts: ' . $e->getMessage());
            return redirect()->route('contacts.index')->with('error', 'Failed to fetch contacts.');
        }
    }

    public function create()
    {
        return view('contacts.create');
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|numeric'
            ]);

            $validatedData['name'] = filter_var($validatedData['name'], FILTER_SANITIZE_STRING);
            $validatedData['phone'] = filter_var($validatedData['phone'], FILTER_SANITIZE_NUMBER_INT);

            Contact::create($validatedData);
            return redirect()->route('contacts.index')->with('success', 'Contact created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating contact: ' . $e->getMessage());
            return redirect()->route('contacts.index')->with('error', 'Failed to create contact.');
        }
    }

    public function show(Contact $contact)
    {
        return view('contacts.show', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        return view('contacts.edit', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
            ]);

            $validatedData['name'] = filter_var($validatedData['name'], FILTER_SANITIZE_STRING);
            if (isset($validatedData['phone'])) {
                $validatedData['phone'] = filter_var($validatedData['phone'], FILTER_SANITIZE_STRING);
            }

            $contact->update($validatedData);
            return redirect()->route('contacts.index')->with('success', 'Contact updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating contact: ' . $e->getMessage());
            return redirect()->route('contacts.index')->with('error', 'Failed to update contact.');
        }
    }

    public function destroy(Contact $contact)
    {
        try {
            $contact->delete();
            return redirect()->route('contacts.index')->with('success', 'Contact deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting contact: ' . $e->getMessage());
            return redirect()->route('contacts.index')->with('error', 'Failed to delete contact.');
        }
    }

    // Bulk import via XML
    public function importXML(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'xml_file' => 'required|mimes:xml',
                'email' => 'required|email'
            ]);

            $validatedData['email'] = filter_var($validatedData['email'], FILTER_SANITIZE_EMAIL);

            $filePath = $request->file('xml_file')->store('imports');
            ImportContactsJob::dispatch($filePath, $validatedData['email']);

            return redirect()->route('contacts.index')->with('success', 'Import started. You will receive an email once it\'s completed.');
        } catch (\Exception $e) {
            Log::error('Error importing contacts: ' . $e->getMessage());
            return redirect()->route('contacts.index')->with('error', 'Failed to start import.');
        }
    }
}
