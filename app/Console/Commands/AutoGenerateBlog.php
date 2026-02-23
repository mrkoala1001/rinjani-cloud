<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Blog;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class AutoGenerateBlog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:auto-generate {topic?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically generate and publish a blog post using CrewAI';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $topic = $this->argument('topic');

        if (!$topic) {
            // Predefined topics or use another AI to generate a trending topic
            $topics = [
                'Manfaat Mikrotik untuk Bisnis Cafe',
                'Cara Mengoptimalkan Jaringan Wi-Fi di Kantor',
                'Mengapa Keamanan Jaringan Penting untuk UMKM',
                'Tren Teknologi Networking di Tahun 2026',
                'Otomatisasi Billing Hotspot dengan Hotpot',
            ];
            $topic = $topics[array_rand($topics)];
        }

        $this->info("Generating blog post for topic: {$topic}...");
        Log::info("Auto-generating blog for topic: {$topic}");

        $pythonBinary = '/usr/bin/python3';
        $scriptPath = base_path('ai_agents/writer.py');
        $command = "HOME=" . storage_path('app/ai_home') . " " . $pythonBinary . " " . $scriptPath . " " . escapeshellarg($topic);

        $output = shell_exec($command);

        if ($output === null) {
            $this->error("Failed to execute AI script.");
            Log::error("Auto-generate blog failed for topic: {$topic} - Script execution failed.");
            return 1;
        }

        // Clean output by extracting content and image prompt
        $content = "";
        $imagePrompt = "";

        if (preg_match('/---CONTENT_START---(.*?)---CONTENT_END---/s', $output, $matches)) {
            $content = trim($matches[1]);
        }

        if (preg_match('/---IMAGE_PROMPT_START---(.*?)---IMAGE_PROMPT_END---/s', $output, $matches)) {
            $imagePrompt = trim($matches[1]);
        }

        if (empty($content)) {
            // Fallback to original output if delimiters not found but check for errors
            if (str_contains($output, 'ERROR:')) {
                $this->error("AI Script Error: " . $output);
                return 1;
            }
            $content = trim($output);
        }

        // Find a default author (builder or first user) from the users_blog table
        $author = User::from('users_blog')->where('role', 'builder')->first() ?? User::from('users_blog')->first();
        if (!$author) {
            $this->error("No author found in users_blog to assign the blog post.");
            return 1;
        }

        // Find a random category or first
        $category = Category::inRandomOrder()->first();

        // Featured Image Logic
        $featuredImage = null;

        // 1. Try to generate/download AI image if prompt exists
        if ($imagePrompt) {
            $this->info("Downloading AI Generated Image...");
            $encodedPrompt = urlencode($imagePrompt);
            // Using Pollinations.ai for free AI image generation
            $imageUrl = "https://image.pollinations.ai/prompt/{$encodedPrompt}?width=1280&height=720&nologo=true&seed=" . rand(1, 1000000);
            
            try {
                $imageData = file_get_contents($imageUrl);
                if ($imageData) {
                    $filename = 'ai_' . time() . '_' . Str::slug(Str::limit($topic, 50)) . '.png';
                    $imagePath = 'blog_images/' . $filename;
                    
                    // Ensure directory exists
                    if (!file_exists(storage_path('app/public/blog_images'))) {
                        mkdir(storage_path('app/public/blog_images'), 0775, true);
                    }

                    file_put_contents(storage_path('app/public/' . $imagePath), $imageData);
                    $featuredImage = $imagePath;
                    $this->info("AI image downloaded successfully: " . $imagePath);
                }
            } catch (\Exception $e) {
                $this->warn("Failed to download AI image, falling back to stock pool: " . $e->getMessage());
            }
        }

        // 2. Fallback to a random image from the folder if AI download failed
        if (!$featuredImage) {
            $imageFiles = glob(storage_path('app/public/blog_images/*.{jpg,jpeg,png,gif}'), GLOB_BRACE);
            if (!empty($imageFiles)) {
                $randomImage = $imageFiles[array_rand($imageFiles)];
                $featuredImage = 'blog_images/' . basename($randomImage);
            }
        }

        try {
            $blog = Blog::create([
                'title' => $topic,
                'slug' => Str::slug($topic),
                'content' => Str::markdown($content),
                'excerpt' => Str::words(strip_tags($content), 20),
                'category_id' => $category ? $category->id : null,
                'featured_image' => $featuredImage,
                'is_published' => true,
                'author_id' => $author->id,
                'published_at' => now(),
            ]);

            $this->info("Blog post created successfully: " . $blog->title);
            Log::info("Automated blog created: " . $blog->title . " ID: " . $blog->id);

            // Post to social media if image URL exists
            if ($imageUrl = env('SOCIAL_MEDIA_AUTO_POST') !== false && !empty($imagePrompt)) {
                $this->info("📱 Posting to social media...");
                
                try {
                    // Extract image URL from output if available
                    $imageUrlToPost = '';
                    if (preg_match('/---IMAGE_URL_START---(.*?)---IMAGE_URL_END---/s', $output, $matches)) {
                        $imageUrlToPost = trim($matches[1]);
                    }
                    
                    if (!empty($imageUrlToPost)) {
                        Artisan::call('social:post', [
                            'article_title' => $topic,
                            'article_content' => substr(strip_tags($content), 0, 300),
                            'image_url' => $imageUrlToPost,
                        ]);
                        $this->info("✅ Posted to social media!");
                    }
                } catch (\Exception $e) {
                    $this->warn("⚠️ Failed to post to social media: " . $e->getMessage());
                    Log::warn("Social media post failed: " . $e->getMessage());
                }
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to save blog post: " . $e->getMessage());
            Log::error("Failed to save automated blog: " . $e->getMessage());
            return 1;
        }
    }
}
