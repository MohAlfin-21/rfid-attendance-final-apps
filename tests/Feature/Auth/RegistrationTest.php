<?php

namespace Tests\Feature\Auth;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    public function test_public_registration_routes_are_not_available(): void
    {
        $this->assertFalse(Route::has('register'));
    }
}
