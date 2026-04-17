<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_change_locale_in_session(): void
    {
        $response = $this
            ->from('/login')
            ->post('/locale', ['locale' => 'en']);

        $response
            ->assertSessionHasNoErrors()
            ->assertSessionHas('locale', 'en')
            ->assertRedirect('/login');
    }

    public function test_authenticated_user_locale_is_persisted_when_changed(): void
    {
        $user = User::factory()->create(['locale' => 'id']);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->post('/locale', ['locale' => 'en']);

        $response
            ->assertSessionHasNoErrors()
            ->assertSessionHas('locale', 'en')
            ->assertRedirect('/profile');

        $this->assertSame('en', $user->fresh()->locale);
    }

    public function test_invalid_locale_is_rejected(): void
    {
        $response = $this
            ->from('/login')
            ->post('/locale', ['locale' => 'fr']);

        $response
            ->assertSessionHasErrors('locale')
            ->assertRedirect('/login');
    }
}
