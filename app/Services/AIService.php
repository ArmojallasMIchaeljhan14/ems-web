<?php

namespace App\Services;

use Illuminate\Http\Client\Request as HttpClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AIService
{
    private array $config;
    
    public function __construct()
    {
        $this->config = config('ai_services');
    }
    
    /**
     * Generate short video from image
     */
    public function generateVideoFromImage($imagePath, array $options = []): ?string
    {
        $service = $this->config['video_services'][$this->config['video_services']['default']] ?? null;
        
        if (!$service || !$service['api_key']) {
            return $this->generateFallbackVideo($imagePath, $options);
        }
        
        try {
            $response = Http::timeout(120)->asMultipart()->post($service['api_url'] . '/video/generate', [
                'image' => fopen(storage_path("app/public/$imagePath"), 'r'),
                'prompt' => $options['prompt'] ?? 'Create a dynamic short video with smooth transitions and engaging effects',
                'duration' => $options['duration'] ?? $this->config['settings']['default_video_duration'],
                'aspect_ratio' => $options['aspect_ratio'] ?? $this->config['settings']['aspect_ratio'],
                'quality' => $options['quality'] ?? $this->config['settings']['video_quality'],
            ], [
                'Authorization' => 'Bearer ' . $service['api_key'],
                'Accept' => 'application/json',
            ]);
            
            if ($response->successful()) {
                $videoData = $response->json();
                if (isset($videoData['video_url'])) {
                    $videoContent = Http::timeout(60)->get($videoData['video_url']);
                    if ($videoContent->successful()) {
                        $filename = 'ai-video-' . time() . '-' . Str::random(8) . '.mp4';
                        $path = 'post-media/' . $filename;
                        Storage::disk('public')->put($path, $videoContent->body());
                        return $path;
                    }
                }
            }
            
            return $this->generateFallbackVideo($imagePath, $options);
        } catch (\Exception $e) {
            \Log::error('AI Video generation failed: ' . $e->getMessage());
            return $this->generateFallbackVideo($imagePath, $options);
        }
    }
    
    /**
     * Generate video from text prompt (event details)
     */
    public function generateVideoFromText(string $prompt, array $options = []): ?string
    {
        $service = $this->config['video_services'][$this->config['video_services']['default']] ?? null;
        
        if (!$service || !$service['api_key']) {
            return $this->generateFallbackTextVideo($prompt, $options);
        }
        
        try {
            $response = Http::timeout(120)->post($service['api_url'] . '/text-to-video', [
                'prompt' => $prompt,
                'duration' => $options['duration'] ?? $this->config['settings']['default_video_duration'],
                'aspect_ratio' => $options['aspect_ratio'] ?? $this->config['settings']['aspect_ratio'],
                'quality' => $options['quality'] ?? $this->config['settings']['video_quality'],
            ], [
                'Authorization' => 'Bearer ' . $service['api_key'],
                'Accept' => 'application/json',
            ]);
            
            if ($response->successful()) {
                $videoData = $response->json();
                if (isset($videoData['video_url'])) {
                    $videoContent = Http::timeout(60)->get($videoData['video_url']);
                    if ($videoContent->successful()) {
                        $filename = 'ai-video-' . time() . '-' . Str::random(8) . '.mp4';
                        $path = 'post-media/' . $filename;
                        Storage::disk('public')->put($path, $videoContent->body());
                        return $path;
                    }
                }
            }
            
            return $this->generateFallbackTextVideo($prompt, $options);
        } catch (\Exception $e) {
            \Log::error('AI Text-to-video generation failed: ' . $e->getMessage());
            return $this->generateFallbackTextVideo($prompt, $options);
        }
    }
    
    /**
     * Enhance image using AI
     */
    public function enhanceImage($imagePath, array $options = []): ?string
    {
        $service = $this->config['image_services'][$this->config['image_services']['default']] ?? null;
        
        if (!$service || !$service['api_key']) {
            return $this->generateFallbackEnhance($imagePath, $options);
        }
        
        try {
            $model = $service['models']['enhance'] ?? null;
            if (!$model) {
                return $this->generateFallbackEnhance($imagePath, $options);
            }
            
            $response = Http::timeout(60)->asMultipart()->post($service['api_url'] . '/predict', [
                'input' => fopen(storage_path("app/public/$imagePath"), 'r'),
                'model' => $model,
                'params' => [
                    'scale' => $options['scale'] ?? 2,
                ],
            ], [
                'Authorization' => 'Bearer ' . $service['api_key'],
                'Accept' => 'application/json',
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['output'][0])) {
                    $imageContent = Http::timeout(30)->get($result['output'][0]);
                    if ($imageContent->successful()) {
                        $filename = 'ai-enhanced-' . time() . '-' . Str::random(8) . '.jpg';
                        $path = 'post-media/' . $filename;
                        Storage::disk('public')->put($path, $imageContent->body());
                        return $path;
                    }
                }
            }
            
            return $this->generateFallbackEnhance($imagePath, $options);
        } catch (\Exception $e) {
            \Log::error('AI Image enhancement failed: ' . $e->getMessage());
            return $this->generateFallbackEnhance($imagePath, $options);
        }
    }
    
    /**
     * Generate advertisement-style content
     */
    public function generateAdvertisement($imagePath, array $options = []): ?string
    {
        $service = $this->config['image_services'][$this->config['image_services']['default']] ?? null;
        
        if (!$service || !$service['api_key']) {
            return $this->generateFallbackAdvertisement($imagePath, $options);
        }
        
        try {
            $model = $service['models']['advertisement'] ?? null;
            if (!$model) {
                return $this->generateFallbackAdvertisement($imagePath, $options);
            }
            
            $response = Http::timeout(60)->asMultipart()->post($service['api_url'] . '/predict', [
                'input' => fopen(storage_path("app/public/$imagePath"), 'r'),
                'model' => $model,
                'params' => [
                    'prompt' => $options['prompt'] ?? 'Create a vibrant, eye-catching advertisement with professional lighting and colors',
                    'scale' => $options['scale'] ?? 1.5,
                    'num_outputs' => 1,
                ],
            ], [
                'Authorization' => 'Bearer ' . $service['api_key'],
                'Accept' => 'application/json',
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['output'][0])) {
                    $imageContent = Http::timeout(30)->get($result['output'][0]);
                    if ($imageContent->successful()) {
                        $filename = 'ai-advertisement-' . time() . '-' . Str::random(8) . '.jpg';
                        $path = 'post-media/' . $filename;
                        Storage::disk('public')->put($path, $imageContent->body());
                        return $path;
                    }
                }
            }
            
            return $this->generateFallbackAdvertisement($imagePath, $options);
        } catch (\Exception $e) {
            \Log::error('AI Advertisement generation failed: ' . $e->getMessage());
            return $this->generateFallbackAdvertisement($imagePath, $options);
        }
    }
    
    /**
     * Fallback video generation using FFmpeg
     */
    private function generateFallbackVideo($imagePath, array $options = []): ?string
    {
        if (!$this->config['settings']['enable_fallback']) {
            return null;
        }
        
        try {
            $ffmpegPath = $this->config['settings']['ffmpeg_path'];
            $duration = $options['duration'] ?? $this->config['settings']['default_video_duration'];
            $filename = 'ai-video-' . time() . '-' . Str::random(8) . '.mp4';
            $path = 'post-media/' . $filename;
            
            // Create a simple animated video from image
            $command = "$ffmpegPath -loop 1 -i " . storage_path("app/public/$imagePath") . " -t $duration -vf scale=1080:1920:flags=lanczos -c:v libx264 -pix_fmt yuv420p " . storage_path("app/public/$path") . " 2>&1";
            
            shell_exec($command);
            
            if (Storage::disk('public')->exists($path)) {
                return $path;
            }
        } catch (\Exception $e) {
            \Log::error('Fallback video generation failed: ' . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Fallback text-to-video using FFmpeg
     */
    private function generateFallbackTextVideo(string $prompt, array $options = []): ?string
    {
        if (!$this->config['settings']['enable_fallback']) {
            return null;
        }
        
        try {
            $ffmpegPath = $this->config['settings']['ffmpeg_path'];
            $duration = $options['duration'] ?? $this->config['settings']['default_video_duration'];
            $filename = 'ai-video-' . time() . '-' . Str::random(8) . '.mp4';
            $path = 'post-media/' . $filename;
            
            // Create a simple video with text overlay
            $command = "$ffmpegPath -f lavfi -i color=gradient:size=1080x1920:duration=$duration -vf \"drawtext=text='$prompt':fontcolor=white:fontsize=32:x=(w-text_w)/2:y=(h-text_h)/2:shadowcolor=black@0.5:shadowx=2:shadowy=2\" -c:v libx264 -pix_fmt yuv420p " . storage_path("app/public/$path") . " 2>&1";
            
            shell_exec($command);
            
            if (Storage::disk('public')->exists($path)) {
                return $path;
            }
        } catch (\Exception $e) {
            \Log::error('Fallback text-to-video failed: ' . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Fallback image enhancement
     */
    private function generateFallbackEnhance($imagePath, array $options = []): ?string
    {
        if (!$this->config['settings']['enable_fallback']) {
            return null;
        }
        
        try {
            $ffmpegPath = $this->config['settings']['ffmpeg_path'];
            $scale = $options['scale'] ?? 2;
            $filename = 'ai-enhanced-' . time() . '-' . Str::random(8) . '.jpg';
            $path = 'post-media/' . $filename;
            
            // Simple enhancement using FFmpeg filters
            $command = "$ffmpegPath -i " . storage_path("app/public/$imagePath") . " -vf scale=iw*$scale:ih*$scale:flags=lanczos,unsharp=5:5:1.0:5:5:0.0 -c:v libx264 -pix_fmt yuv420p " . storage_path("app/public/$path") . " 2>&1";
            
            shell_exec($command);
            
            if (Storage::disk('public')->exists($path)) {
                return $path;
            }
        } catch (\Exception $e) {
            \Log::error('Fallback image enhancement failed: ' . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Fallback advertisement generation
     */
    private function generateFallbackAdvertisement($imagePath, array $options = []): ?string
    {
        if (!$this->config['settings']['enable_fallback']) {
            return null;
        }
        
        try {
            $ffmpegPath = $this->config['settings']['ffmpeg_path'];
            $filename = 'ai-advertisement-' . time() . '-' . Str::random(8) . '.jpg';
            $path = 'post-media/' . $filename;
            
            // Create advertisement-style version with enhanced colors and contrast
            $command = "$ffmpegPath -i " . storage_path("app/public/$imagePath") . " -vf eq=brightness=0.1:contrast=1.3:saturation=1.2,scale=iw*1.2:ih*1.2:flags=lanczos -c:v libx264 -pix_fmt yuv420p " . storage_path("app/public/$path") . " 2>&1";
            
            shell_exec($command);
            
            if (Storage::disk('public')->exists($path)) {
                return $path;
            }
        } catch (\Exception $e) {
            \Log::error('Fallback advertisement generation failed: ' . $e->getMessage());
        }
        
        return null;
    }
}
