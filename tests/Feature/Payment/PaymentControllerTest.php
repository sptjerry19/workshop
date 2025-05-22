<?php

namespace Tests\Feature\Payment;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Firebase\JWT\JWT;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->generateToken($this->user);
    }

    private function generateToken($user)
    {
        $payload = [
            'iss' => "laravel-jwt",
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + 60 * 60 * 24
        ];

        return JWT::encode($payload, env('JWT_SECRET'), 'HS256');
    }

    public function test_user_can_create_payment()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/payment/create', [
                'amount' => 100,
                'currency' => 'USD',
                'description' => 'Test payment'
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'payment_url',
                'payment_id'
            ]);
    }

    public function test_payment_webhook_handles_notification()
    {
        $response = $this->postJson('/api/payment/ipn', [
            'payment_id' => 'test_payment_id',
            'status' => 'completed',
            'amount' => 100,
            'currency' => 'USD'
        ]);

        $response->assertStatus(200);
    }

    public function test_payment_redirect_handles_success()
    {
        $response = $this->get('/api/payment/redirect?status=success&payment_id=test_payment_id');

        $response->assertStatus(200);
    }
}
