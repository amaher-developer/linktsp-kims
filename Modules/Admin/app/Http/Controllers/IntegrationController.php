<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Integration\Models\Integration;

/**
 * Phase 1 manages a single Foodics integration record (credentials form
 * only). Sync jobs, webhook handling, and multi-integration support are
 * out of scope until the Foodics integration phase.
 */
class IntegrationController extends Controller
{
    public function edit(): View
    {
        $integration = Integration::firstOrCreate(
            ['provider' => 'foodics'],
            ['name' => 'Foodics', 'status' => 'inactive', 'credentials' => json_encode([])]
        );

        return view('admin::admin.integrations.edit', ['integration' => $integration]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'status' => ['required', 'in:active,inactive'],
            'client_id' => ['nullable', 'string'],
            'client_secret' => ['nullable', 'string'],
        ]);

        $integration = Integration::firstOrCreate(
            ['provider' => 'foodics'],
            ['name' => 'Foodics', 'status' => 'inactive', 'credentials' => json_encode([])]
        );

        $credentials = $data['client_id'] || $data['client_secret']
            ? ['client_id' => $data['client_id'], 'client_secret' => $data['client_secret']]
            : json_decode($integration->credentials ?? '{}', true);

        $integration->update([
            'name' => $data['name'],
            'status' => $data['status'],
            'credentials' => json_encode($credentials),
        ]);

        return redirect()->route('admin.integrations.edit')->with('status', 'saved');
    }
}
