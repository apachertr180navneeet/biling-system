<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;
use App\Services\PaymentGatewayService;
use Exception;

class PaymentGatewayController extends Controller
{
    public function index()
    {
        $gateways = PaymentGateway::orderBy('name')->get();
        return view('admin.payment-gateways.index', compact('gateways'));
    }

    public function update(Request $request, PaymentGateway $gateway)
    {
        try {
            $request->validate([
                'mode' => 'required|in:test,live',
                'is_active' => 'nullable',
                'credentials' => 'nullable|array',
            ]);

            $credentials = [];
            $credentialKeys = $this->getCredentialFields($gateway->slug);
            foreach ($credentialKeys as $key => $label) {
                $credentials[$key] = $request->input("credentials.{$key}", '');
            }

            $gateway->update([
                'mode' => $request->mode,
                'is_active' => $request->boolean('is_active'),
                'credentials' => $credentials,
            ]);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => "{$gateway->name} settings updated successfully!"]);
            }

            return back()->with('success', "{$gateway->name} settings updated successfully!");
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function toggleStatus(PaymentGateway $gateway)
    {
        try {
            $gateway->update(['is_active' => !$gateway->is_active]);
            $status = $gateway->is_active ? 'activated' : 'deactivated';

            return response()->json(['success' => true, 'is_active' => $gateway->is_active, 'message' => "{$gateway->name} {$status} successfully!"]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function testConnection(PaymentGateway $gateway)
    {
        try {
            $service = new PaymentGatewayService();
            $result = $service->createOrder($gateway, 100.00, 'INR', ['test' => true]);

            return response()->json([
                'success' => true,
                'message' => "{$gateway->name} connection successful! ({$gateway->mode} mode)",
                'details' => $result,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "{$gateway->name} connection failed: {$e->getMessage()}",
            ], 422);
        }
    }

    public function seed()
    {
        $gateways = [
            [
                'name' => 'Razorpay',
                'slug' => 'razorpay',
                'description' => 'Accept payments via UPI, Cards, Netbanking, Wallets through Razorpay',
                'mode' => 'test',
                'is_active' => false,
                'credentials' => [
                    'key_id' => env('RAZORPAY_KEY_ID', ''),
                    'key_secret' => env('RAZORPAY_KEY_SECRET', ''),
                ],
                'settings' => [
                    'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET', ''),
                    'supported_methods' => ['upi', 'card', 'netbanking', 'wallet'],
                ],
            ],
            [
                'name' => 'Stripe',
                'slug' => 'stripe',
                'description' => 'Accept international card payments via Stripe',
                'mode' => 'test',
                'is_active' => false,
                'credentials' => [
                    'publishable_key' => env('STRIPE_PUBLISHABLE_KEY', ''),
                    'secret_key' => env('STRIPE_SECRET_KEY', ''),
                    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET', ''),
                ],
                'settings' => [
                    'supported_currencies' => ['USD', 'EUR', 'GBP', 'INR'],
                ],
            ],
        ];

        foreach ($gateways as $data) {
            PaymentGateway::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }

        return back()->with('success', 'Payment gateways seeded successfully!');
    }

    public function getCredentialFields(string $slug): array
    {
        return match ($slug) {
            'razorpay' => [
                'key_id' => 'API Key ID',
                'key_secret' => 'API Key Secret',
            ],
            'stripe' => [
                'publishable_key' => 'Publishable Key',
                'secret_key' => 'Secret Key',
                'webhook_secret' => 'Webhook Secret',
            ],
            default => [],
        };
    }
}
