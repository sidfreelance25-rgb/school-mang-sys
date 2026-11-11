<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible(): void
    {
        $this->get(route('login'))->assertOk();
    }

    public function test_admin_can_view_dashboard_after_login(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@school.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dashboard');
    }

    public function test_student_cannot_access_settings(): void
    {
        $this->seed();

        $student = User::query()->where('role', UserRole::Student)->firstOrFail();

        $this->actingAs($student)
            ->get(route('settings.edit'))
            ->assertForbidden();
    }

    public function test_accountant_can_open_fees_module(): void
    {
        $this->seed();

        $accountant = User::query()->where('email', 'accountant@school.test')->firstOrFail();

        $this->actingAs($accountant)
            ->get(route('fees.index'))
            ->assertOk()
            ->assertSee('Fee Management');
    }
}
