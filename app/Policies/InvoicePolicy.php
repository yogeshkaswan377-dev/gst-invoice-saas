<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    /**
     * User can only access invoices from their company
     */
    public function view(User $user, Invoice $invoice): bool
    {
        return $user->current_company_id === $invoice->company_id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['owner', 'admin']);
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->current_company_id === $invoice->company_id
            && $user->hasAnyRole(['owner', 'admin'])
            && in_array($invoice->status, ['draft']);
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->current_company_id === $invoice->company_id
            && $user->hasAnyRole(['owner', 'admin'])
            && in_array($invoice->status, ['draft']);
    }

    public function sendEmail(User $user, Invoice $invoice): bool
    {
        return $user->current_company_id === $invoice->company_id;
    }

    public function downloadPdf(User $user, Invoice $invoice): bool
    {
        return $user->current_company_id === $invoice->company_id;
    }
}
