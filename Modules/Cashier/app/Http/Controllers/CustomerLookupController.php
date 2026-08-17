<?php

namespace Modules\Cashier\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Cashier\Http\Requests\IdentifyCustomerRequest;
use Modules\Ordering\Http\Resources\CustomerResource;
use Modules\Ordering\Models\CustomerIdentifier;

class CustomerLookupController extends Controller
{
    /**
     * Identify a customer by their KIMS QR/barcode. This is the cashier's
     * only entry point into a customer's identity — it never accepts a
     * manually typed amount, per the schema's loyalty protection rule.
     */
    public function store(IdentifyCustomerRequest $request): CustomerResource
    {
        $identifier = CustomerIdentifier::where('value', $request->string('code'))
            ->where('is_active', true)
            ->with('customer.loyaltyAccount')
            ->first();

        abort_unless($identifier && $identifier->customer?->is_active, 404, 'Customer not found for this code.');

        return new CustomerResource($identifier->customer);
    }
}
