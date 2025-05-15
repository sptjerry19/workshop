<?php

namespace Tests\Feature\Payment;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    public function test_user_can_create_payment()
    {
        $paymentData = [
            'amount' => 100,
            'currency' => 'USD',
            'description' => 'Test payment'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/payment/create', $paymentData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'payment' => [
                    'id',
                    'amount',
                    'currency',
                    'description',
                    'status'
                ]
            ]);
    }

    public function test_payment_ipn_can_be_handled()
    {
        $ipnData = [
            'transaction_id' => 'TEST123',
            'status' => 'completed',
            'amount' => 100,
            'currency' => 'USD'
        ];

        $response = $this->postJson('/api/payment/ipn', $ipnData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function test_payment_redirect_can_be_handled()
    {
        $redirectData = [
            'transaction_id' => 'TEST123',
            'status' => 'completed'
        ];

        $response = $this->getJson('/api/payment/redirect?' . http_build_query($redirectData));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function test_unauthenticated_user_cannot_create_payment()
    {
        $paymentData = [
            'amount' => 100,
            'currency' => 'USD',
            'description' => 'Test payment'
        ];

        $response = $this->postJson('/api/payment/create', $paymentData);

        $response->assertStatus(401);
    }
}