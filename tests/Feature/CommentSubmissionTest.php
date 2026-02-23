<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Comment;

class CommentSubmissionTest extends TestCase
{
     // Note: Environment setup is tricky as established in previous turn (no sqlite driver).
     // We will try to rely on the MySQL test database if possible, or Mocking.
     // But `refreshDatabase` might fail if connection is not set up.
     // I'll skip RefreshDatabase trait for now and just insert/delete manually if needed, 
     // or just rely on the test failing if DB is unreachable.
     
     // Actually, I can use the `hotpot_test` DB if I configure it in phpunit.xml?
     // For now, let's just write the test and see. If it fails due to DB, I'll know.
    
    public function test_user_can_submit_comment()
    {
        $response = $this->post(route('comment.store'), [
            'name' => 'Mr. Tester',
            'email' => 'tester@example.com',
            'content' => 'This is a test comment.',
        ]);

        $response->assertStatus(302); // Should redirect back
        $response->assertSessionHas('success');
        
        // Assert database has the comment
        // Since I can't easily rely on DB in this environment (as seen before), 
        // I might need to inspect the code more or try to fix the query logic blindly if the test explodes.
        // But let's try.
        $this->assertDatabaseHas('comments', [
             'email' => 'tester@example.com',
        ]);
    }
}
