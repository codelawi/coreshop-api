<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function index(): JsonResponse
    {
        $addresses = Address::where('user_id', Auth::id())
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $addresses,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateData($request);

        $address = DB::transaction(function () use ($data) {
            if ($data['is_default'] ?? false) {
                Address::where('user_id', Auth::id())->update(['is_default' => false]);
            }
            $data['user_id'] = Auth::id();
            $data['label'] = $data['label'] ?? 'Home';
            $data['address_line'] = $data['address_line'] ?? '';

            // First address auto-default
            if (Address::where('user_id', Auth::id())->count() === 0) {
                $data['is_default'] = true;
            }

            return Address::create($data);
        });

        return response()->json([
            'success' => true,
            'message' => 'Address added',
            'data' => $address,
        ], 201);
    }

    public function show(Address $address): JsonResponse
    {
        $this->authorize($address);

        return response()->json(['success' => true, 'data' => $address]);
    }

    public function update(Request $request, Address $address): JsonResponse
    {
        $this->authorize($address);
        $data = $this->validateData($request);

        DB::transaction(function () use ($data, $address) {
            if ($data['is_default'] ?? false) {
                Address::where('user_id', Auth::id())
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }
            $data['label'] = $data['label'] ?? 'Home';
            $data['address_line'] = $data['address_line'] ?? '';
            $address->update($data);
        });

        return response()->json([
            'success' => true,
            'message' => 'Address updated',
            'data' => $address->fresh(),
        ]);
    }

    public function destroy(Address $address): JsonResponse
    {
        $this->authorize($address);
        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $next = Address::where('user_id', Auth::id())->first();
            $next?->update(['is_default' => true]);
        }

        return response()->json(['success' => true, 'message' => 'Address removed']);
    }

    public function setDefault(Address $address): JsonResponse
    {
        $this->authorize($address);

        DB::transaction(function () use ($address) {
            Address::where('user_id', Auth::id())->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });

        return response()->json(['success' => true, 'message' => 'Default address updated']);
    }

    private function authorize(Address $address): void
    {
        abort_unless($address->user_id === Auth::id(), 403, 'Unauthorized');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'label' => ['nullable', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'address_line' => ['nullable', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:50'],
            'floor' => ['nullable', 'string', 'max:50'],
            'apartment' => ['nullable', 'string', 'max:50'],
            'city' => ['required', 'string', 'max:100'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'notes' => ['nullable', 'string', 'max:500'],
            'is_default' => ['nullable', 'boolean'],
        ]);
    }
}
