<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\EnsuresCounterpartyOwnership;
use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Counterparty;
use Illuminate\Http\Request;

/**
 * Точечный CRUD контактов контрагента — каждый контакт редактируется
 * независимо (попап на конкретной строке), поэтому не нужен весь массив
 * контактов разом, как было раньше в CounterpartyController. Меняем/создаём/
 * удаляем ровно одну запись за раз.
 */
class ContactController extends Controller
{
    use EnsuresCounterpartyOwnership;

    public function store(Request $request, Company $company, Counterparty $counterparty)
    {
        $this->ensureBelongsToCompany($company, $counterparty);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $contact = $counterparty->contacts()->create($validated);

        return (new ContactResource($contact))->response()->setStatusCode(201);
    }

    public function update(Request $request, Company $company, Counterparty $counterparty, Contact $contact)
    {
        $this->ensureBelongsToCompany($company, $counterparty);
        $this->ensureBelongsToCounterparty($counterparty, $contact);

        $validated = $request->validate([
            // sometimes — чтобы можно было прислать только одно изменившееся
            // поле (например, только name), не пересылая остальные заново.
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'position' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ]);

        $contact->update($validated);

        return new ContactResource($contact);
    }

    public function destroy(Company $company, Counterparty $counterparty, Contact $contact)
    {
        $this->ensureBelongsToCompany($company, $counterparty);
        $this->ensureBelongsToCounterparty($counterparty, $contact);

        $contact->delete();

        return response()->json(['message' => 'Контакт удалён']);
    }
}
