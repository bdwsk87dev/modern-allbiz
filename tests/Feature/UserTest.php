<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\User;

class UserTest extends TestCase
{
    use WithFaker;
    private $password = "1561615616";
    public function testGetUserToken()
    {
        $email = "context@optimozg.com";
        $password = "1561615616";
        $response = $this->postJson('/api/auth/login', [
            'email' => $email,
            'password' => $password,
            'remember_me' => true
        ]);
        $response
            ->assertStatus(200)
            ->assertExactJson([
                'message' => "Successfully!",
            ]);
    }
}