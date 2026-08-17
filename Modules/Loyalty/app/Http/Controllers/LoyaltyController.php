<?php

namespace Modules\Loyalty\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Loyalty\Http\Resources\LoyaltyAccountResource;
use Modules\Loyalty\Http\Resources\LoyaltyTransactionResource;

class LoyaltyController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $account = $request->user()->loyaltyAccount;

        if (! $account) {
            // create() doesn't hydrate the DB-computed defaults (balance,
            // lifetime_earned/redeemed all default to 0 at the schema
            // level, deliberately excluded from $fillable) — refresh to
            // read them back rather than serializing an unset attribute.
            $account = $request->user()->loyaltyAccount()->create(['status' => 'active'])->refresh();
        }

        // Always 200: lazily creating the account on first access is an
        // implementation detail, not something the client asked to create.
        return (new LoyaltyAccountResource($account))->response()->setStatusCode(200);
    }

    public function transactions(Request $request): AnonymousResourceCollection
    {
        $transactions = $request->user()->loyaltyTransactions()
            ->latest('created_at')
            ->paginate(20);

        return LoyaltyTransactionResource::collection($transactions);
    }
}
