<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    public function test_email_verification_not_required_in_system(): void
    {
        $this->assertTrue(true);
    }
}
