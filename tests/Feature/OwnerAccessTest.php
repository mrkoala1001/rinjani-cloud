<?php

namespace Tests\Feature;

/**
 * Note: This test requires a database connection (SQLite or MySQL).
 * Currently, the environment lacks the SQLite driver and permissions to create a test MySQL database.
 * To run this test, ensure a valid test database is configured in phpunit.xml.
 */
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OwnerAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_sees_correct_routes_on_report_page()
    {
        // Create an Owner user
        $owner = User::factory()->create([
            'role' => 'owner',
            'email' => 'owner@test.com',
            'password' => bcrypt('password'),
        ]);

        // Login
        $this->actingAs($owner);

        // Visit Report Page
        $response = $this->get(route('report.form'));
        
        $response->assertStatus(200);

        // Debug output if needed
        // dump($response->getContent());

        // BEFORE FIX: These should fail if I assert the CORRECT routes, 
        // or pass if I assert the WRONG routes (to confirm reproduction).
        // The task is to "Create a reproduction test". 
        // So I will write assertions that expect the CORRECT behavior, causing the test to FAIL currently.
        
        // Assert Form Action is correct (Owner should post to /report, alias report.send)
        $response->assertSee('action="' . route('report.send') . '"', false);
        
        // Assert Link to Tickets is correct (Owner should go to /reports, alias report.index)
        $response->assertSee('href="' . route('report.index') . '"', false);
        
        // Assert we do NOT see hotsupport routes
        $response->assertDontSee('hotsupport/tickets');
    }

    public function test_owner_cannot_access_hotsupport_tickets()
    {
         $owner = User::factory()->create([
            'role' => 'owner',
            'email' => 'owner_fail@test.com',
        ]);

        $this->actingAs($owner);

        // Try to access ISP only route
        $response = $this->get('/hotsupport/tickets');
        
        // Should be forbidden
        $response->assertStatus(403);
    }
}
