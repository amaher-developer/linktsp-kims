<?php

namespace Modules\Cashier\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Ordering\Http\Resources\InvoiceResource;
use Modules\Ordering\Models\Invoice;

class InvoiceController extends Controller
{
    /**
     * Look up an invoice by number for verification before awarding
     * loyalty points. Fetching/validating the invoice against Foodics
     * itself is out of scope until the Foodics integration phase — this
     * only reads what's already recorded in kims_invoices.
     */
    public function show(string $invoiceNumber): InvoiceResource
    {
        $invoice = Invoice::where('invoice_number', $invoiceNumber)->firstOrFail();

        return new InvoiceResource($invoice);
    }
}
