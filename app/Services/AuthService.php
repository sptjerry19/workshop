<?php

namespace App\Services;

use App\Jobs\SendContactEmail;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class AuthService extends BaseService
{
    protected Model $model;

    public function __construct()
    {
        $this->model = new Contact();
    }

    public function getAllContacts()
    {
        try {
            $contacts = Contact::orderBy('created_at', 'desc')
                ->select('id', 'name', 'email', 'subject', 'phone', 'type')
                ->paginate(10);
            return $this->transformPaginationResult($contacts, fn($item) => $item->transform());
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getContactById($id)
    {
        try {
            $contact = Contact::findOrFail($id);
            return $contact->transform();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createContact(array $data)
    {
        try {
            $contact = Contact::create($data);
            // dispatch contact to job queue to send email
            dispatch(new SendContactEmail($contact));
            return $contact->transform();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }
    public function updateContact($id, array $data)
    {
        try {
            $contact = Contact::findOrFail($id);
            $contact->update($data);
            return $contact->transform();
        } catch (\Exception $e) {
            return false;
        }
    }
    public function deleteContact($id)
    {
        try {
            $contact = Contact::findOrFail($id);
            $contact->delete();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
