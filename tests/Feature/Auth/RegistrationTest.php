<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_registration_screen_redirects_to_login(): void
    {
        $response = $this->get('/register');

        $response->assertRedirect('/login');
    }
}
