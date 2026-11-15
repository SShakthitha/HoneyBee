<?php

namespace Tests\Feature;

use App\Models\Staff;
use App\Models\StaffInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StaffInvitationRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $staff = Staff::create(['full_name' => 'Admin', 'role' => 'admin', 'email' => 'admin@example.test', 'phone' => '0771234567', 'hire_date' => now()->toDateString()]);
        return User::factory()->create(['name' => $staff->full_name, 'email' => $staff->email, 'email_verified_at' => now()]);
    }

    private function staff(): Staff
    {
        return Staff::create(['full_name' => 'Invited Staff', 'role' => 'staff', 'email' => 'invited@example.test', 'phone' => '0771234567', 'hire_date' => now()->toDateString()]);
    }

    private function invite(?Staff $staff = null): array
    {
        $staff ??= $this->staff();
        $token = str_repeat('a', 64);
        $invitation = StaffInvitation::create(['staff_id' => $staff->staff_id, 'token_hash' => hash('sha256', $token), 'expires_at' => now()->addDay()]);
        return [$staff, $invitation, $token];
    }

    public function test_admin_can_generate_and_regenerate_a_copyable_registration_link(): void
    {
        $admin = $this->admin();
        $staff = $this->staff();
        $response = $this->actingAs($admin)->post(route('admin.staff.invitation', $staff->staff_id));
        $response->assertRedirect(route('admin.staff'))->assertSessionHas('invitation');
        $oldHash = StaffInvitation::firstOrFail()->token_hash;
        $url = $response->getSession()->get('invitation')['url'];
        $this->assertMatchesRegularExpression('~^https?://[^/]+/staff/register-invitation/[A-Za-z0-9]+$~', $url);
        $response = $this->actingAs($admin)->post(route('admin.staff.invitation', $staff->staff_id));
        $response->assertRedirect(route('admin.staff'));
        $this->assertNotSame($oldHash, StaffInvitation::firstOrFail()->token_hash);
        $this->actingAs($admin)->get(route('admin.staff'))->assertOk()->assertSee('Copy Registration Link')->assertSee('Registration link copied successfully!');
    }

    public function test_unauthorized_user_cannot_issue_invitation(): void
    {
        $staff = $this->staff();
        $this->actingAs(User::factory()->create())->post(route('admin.staff.invitation', $staff->staff_id))->assertForbidden();
    }

    public function test_valid_invitation_registers_linked_staff_email_and_single_use_account(): void
    {
        $admin = $this->admin();
        [$staff, $invitation, $token] = $this->invite();
        $this->get(route('staff.invitation.register', $token))->assertOk()->assertSee('Invited Staff')->assertSee('invited@example.test');
        $this->actingAs($admin)->get('http://127.0.0.1:8000/login')->assertRedirect('http://127.0.0.1:8000/dashboard');
        $this->get('http://127.0.0.1:8000/dashboard')->assertForbidden();
        $this->actingAs($admin)->post('http://127.0.0.1:8000/staff/register-invitation/'.$token, ['name' => 'Changed', 'email' => 'changed@example.test', 'role' => 'admin', 'password' => 'StrongPassword123!', 'password_confirmation' => 'StrongPassword123!'])->assertRedirect('http://127.0.0.1:8000/login');
        $this->assertGuest();
        $this->get('http://127.0.0.1:8000/login')->assertOk();
        $user = User::where('email', $staff->email)->firstOrFail();
        $this->assertSame('Invited Staff', $user->name);
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertTrue(Hash::check('StrongPassword123!', $user->password));
        $this->assertNotSame('StrongPassword123!', $user->password);
        $this->assertNotNull($invitation->fresh()->accepted_at);
        $this->assertDatabaseCount('staff', 2);
        $this->get(route('staff.invitation.register', $token))->assertOk()->assertSee('invalid or has already been used');
        $this->post('http://127.0.0.1:8000/login', ['email' => $staff->email, 'password' => 'StrongPassword123!'])->assertRedirect('http://127.0.0.1:8000/staff/dashboard');
        $this->assertAuthenticatedAs($user);
        $this->get('http://127.0.0.1:8000/staff/dashboard')->assertOk();
    }

    public function test_invalid_expired_and_used_invitation_links_are_rejected(): void
    {
        $this->get(route('staff.invitation.register', 'invalid'))->assertOk()->assertSee('invalid or has already been used');
        [$staff, $invitation, $token] = $this->invite();
        $invitation->update(['expires_at' => now()->subSecond()]);
        $this->get(route('staff.invitation.register', $token))->assertOk()->assertSee('expired');
        $this->post(route('staff.invitation.store', $token), ['password' => 'StrongPassword123!', 'password_confirmation' => 'StrongPassword123!'])->assertStatus(410);
    }

    public function test_duplicate_existing_user_blocks_invitation_acceptance(): void
    {
        [$staff, , $token] = $this->invite();
        User::factory()->create(['email' => $staff->email]);
        $this->get(route('staff.invitation.register', $token))->assertOk()->assertSee('already exists');
        $this->post(route('staff.invitation.store', $token), ['password' => 'StrongPassword123!', 'password_confirmation' => 'StrongPassword123!'])->assertStatus(410);
    }
}
