<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use DatabaseTransactions;

    public function test_reset_password_link_screen_redirects_to_login(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertRedirect('/login');
    }

    public function test_reset_password_screen_redirects_to_login(): void
    {
        $response = $this->get('/reset-password/test-token');

        $response->assertRedirect('/login');
    }
}
