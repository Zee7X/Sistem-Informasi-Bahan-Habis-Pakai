<?php

namespace Tests\SmokeTest;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserCrudSmokeTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private User $mahasiswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test_users@bhp.com'],
            ['name' => 'Admin Test Users', 'password' => bcrypt('12345'), 'role' => 'admin']
        );

        $this->mahasiswa = User::firstOrCreate(
            ['email' => 'mhs_test_users@bhp.com'],
            ['name' => 'Mahasiswa Test Users', 'password' => bcrypt('12345'), 'role' => 'mahasiswa']
        );
    }

    /** 1. Positive: Admin can list users */
    public function test_admin_can_view_users_list(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));
        $response->assertStatus(200);
    }

    /** 2. Positive: Admin can create new user */
    public function test_admin_can_create_user(): void
    {
        $email = 'new_user_' . uniqid() . '@example.com';
        $payload = [
            'name'          => 'Budi Santoso',
            'email'         => $email,
            'password'      => 'secret123',
            'role'          => 'mahasiswa',
            'nim'           => '2026' . rand(1000, 9999),
            'kelas'         => 'TI-2A',
            'program_studi' => 'Teknik Informatika',
            'angkatan'      => '2026',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), $payload);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'name'  => 'Budi Santoso',
            'role'  => 'mahasiswa',
        ]);
    }

    /** 3. Positive: Admin can update user data */
    public function test_admin_can_update_user(): void
    {
        $target = User::create([
            'name'     => 'User Update ' . uniqid(),
            'email'    => 'user_update_' . uniqid() . '@example.com',
            'password' => bcrypt('12345'),
            'role'     => 'mahasiswa',
        ]);

        $updatePayload = [
            'name'          => 'User Updated Name',
            'email'         => $target->email,
            'role'          => 'ketua_jurusan',
            'nim'           => 'NIP123456',
            'kelas'         => null,
            'program_studi' => 'Farmasi',
            'angkatan'      => null,
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $target), $updatePayload);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id'   => $target->id,
            'name' => 'User Updated Name',
            'role' => 'ketua_jurusan',
        ]);
    }

    /** 4. Positive: Admin can delete another user */
    public function test_admin_can_delete_another_user(): void
    {
        $target = User::create([
            'name'     => 'User Delete ' . uniqid(),
            'email'    => 'user_del_' . uniqid() . '@example.com',
            'password' => bcrypt('12345'),
            'role'     => 'mahasiswa',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $target));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    /** 5. Negative: Validation fails on empty mandatory fields */
    public function test_create_user_fails_with_empty_required_fields(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name'     => '',
            'email'    => '',
            'password' => '',
            'role'     => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password', 'role']);
    }

    /** 6. Negative: Validation fails on invalid email format and short password */
    public function test_create_user_fails_with_invalid_email_and_short_password(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name'     => 'Bad Input User',
            'email'    => 'not-an-email',
            'password' => '123', // min:6
            'role'     => 'invalid_role', // must be admin, mahasiswa, ketua_jurusan
        ]);

        $response->assertSessionHasErrors(['email', 'password', 'role']);
    }

    /** 7. Negative: Validation fails on duplicate email */
    public function test_create_user_fails_with_duplicate_email(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name'     => 'Duplicate Email User',
            'email'    => $this->admin->email,
            'password' => 'secret123',
            'role'     => 'mahasiswa',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** 8. Negative: Self-deletion is blocked */
    public function test_admin_cannot_delete_own_account(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->admin));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    /** 9. Negative: Non-admin is forbidden from user management */
    public function test_non_admin_cannot_manage_users(): void
    {
        $response = $this->actingAs($this->mahasiswa)->get(route('admin.users.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->mahasiswa)->post(route('admin.users.store'), [
            'name'     => 'Hacker',
            'email'    => 'hack@bhp.com',
            'password' => '123456',
            'role'     => 'admin',
        ]);
        $response->assertStatus(403);
    }
}
