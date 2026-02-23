<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class PostToSocialMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'social:post {article_title} {article_content} {image_url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and post content to Facebook & Instagram';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $title = $this->argument('article_title');
        $content = $this->argument('article_content');
        $imageUrl = $this->argument('image_url');

        $this->info('🤖 Generating social media content...');
        
        // Try CrewAI social media agent first
        $socialData = $this->generateSocialContentWithAgent($title, $content, $imageUrl);
        
        // Fallback to simple caption generator if agent fails
        if (!$socialData) {
            $this->warn('⚠️ Agent error, using simple caption generator...');
            $socialData = $this->generateSimpleCaptions($title, $content);
        }

        if (!$socialData) {
            $this->error('Failed to generate social media content.');
            return 1;
        }

        // Post to Instagram (akan otomatis share ke Facebook)
        $this->postToInstagram($socialData, $imageUrl, $title);

        return 0;
    }

    /**
     * Try to generate content using CrewAI agent
     */
    private function generateSocialContentWithAgent($title, $content, $imageUrl)
    {
        try {
            // Run Python script to generate social content
            $pythonBinary = '/usr/bin/python3';
            $scriptPath = base_path('ai_agents/social_media_agent.py');
            $command = "HOME=" . storage_path('app/ai_home') . " " . $pythonBinary . " " . $scriptPath . " " . 
                       escapeshellarg($title) . " " . 
                       escapeshellarg($content) . " " . 
                       escapeshellarg($imageUrl);

            $output = shell_exec($command);

            if ($output === null) {
                return null;
            }

            // Parse output
            if (preg_match('/---SOCIAL_CONTENT_START---(.*?)---SOCIAL_CONTENT_END---/s', $output, $matches)) {
                $socialContent = trim($matches[1]);
                return json_decode($socialContent, true);
            }

            return null;
        } catch (\Exception $e) {
            Log::warn('Social media agent error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate simple captions without LLM
     */
    private function generateSimpleCaptions($title, $content)
    {
        try {
            $pythonBinary = '/usr/bin/python3';
            $scriptPath = base_path('ai_agents/simple_caption_gen.py');
            $command = "HOME=" . storage_path('app/ai_home') . " " . $pythonBinary . " " . $scriptPath . " " . 
                       escapeshellarg($title) . " " . 
                       escapeshellarg($content);

            $output = shell_exec($command);

            if ($output === null) {
                return null;
            }

            return json_decode(trim($output), true);
        } catch (\Exception $e) {
            Log::error('Simple caption generator error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Post to Instagram (akan otomatis share ke Facebook)
     */
    private function postToInstagram($socialData, $imageUrl, $title)
    {
        try {
            $igAccountId = env('INSTAGRAM_BUSINESS_ACCOUNT_ID');
            $pageToken = env('FACEBOOK_PAGE_ACCESS_TOKEN');
            $apiVersion = env('FACEBOOK_API_VERSION', 'v18.0');

            if (!$igAccountId || !$pageToken) {
                $this->warn('⚠️ Instagram credentials not configured.');
                return false;
            }

            // Build Instagram caption
            $caption = $socialData['instagram_feed'] ?? '📝 Baca artikel baru!';
            $hashtags = implode(' ', (array)($socialData['hashtags'] ?? []));
            
            if ($hashtags) {
                $caption .= "\n\n" . $hashtags;
            }
            
            $caption .= "\n\n🔗 Link di bio";

            $this->info("📸 Posting to Instagram...");
            $this->info("Caption: " . substr($caption, 0, 100) . "...");

            // Create media container
            $response = Http::post(
                "https://graph.instagram.com/{$apiVersion}/{$igAccountId}/media",
                [
                    'image_url' => $imageUrl,
                    'caption' => $caption,
                    'access_token' => $pageToken,
                ]
            );

            if (!$response->successful()) {
                $this->warn('⚠️ Instagram API: ' . $response->json('error.message', 'Unknown error'));
                $this->info("📝 Caption siap di-post manual: ");
                $this->info($caption);
                return false;
            }

            $mediaId = $response->json('id');

            // Publish media
            $publishResponse = Http::post(
                "https://graph.instagram.com/{$apiVersion}/{$igAccountId}/media_publish",
                [
                    'creation_id' => $mediaId,
                    'access_token' => $pageToken,
                ]
            );

            if ($publishResponse->successful()) {
                $this->info('✅ Posted to Instagram! (akan otomatis share ke Facebook)');
                Log::info('Instagram post created: ' . $publishResponse->json('id'));
                return true;
            } else {
                $this->warn('⚠️ Instagram publish failed: ' . $publishResponse->json('error.message', 'Unknown error'));
                $this->info("📝 Caption ready untuk di-post manual: ");
                $this->info($caption);
                return false;
            }

        } catch (\Exception $e) {
            $this->warn('⚠️ Instagram posting error: ' . $e->getMessage());
            Log::warn('Instagram posting exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Post to Facebook (DEPRECATED - Instagram akan auto-share ke FB)
     */
    private function postToFacebook($socialData, $imageUrl)
    {
        // Deprecated - Instagram will auto-share to Facebook
        return true;
    }
}
