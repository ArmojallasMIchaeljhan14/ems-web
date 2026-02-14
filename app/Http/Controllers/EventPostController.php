<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventPost;
use App\Services\AIService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EventPostController extends Controller
{
    private AIService $aiService;
    
    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }
    public function create(): View
    {
        $events = Event::query()
            ->where('status', 'published')  // Only show published events
            ->orderByDesc('start_at')
            ->get();

        return view('multimedia.posts.create', compact('events'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'type' => ['required', 'in:invitation,announcement,highlight,thank_you,reminder,advertisement'],
            'caption' => ['nullable', 'string', 'max:5000'],
            'ai_prompt' => ['nullable', 'string', 'max:1000'],
            'media.*' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,mp4,mov,avi', 'max:10240'], // 10MB max
            'generate_ai_content' => ['nullable', 'boolean'],
            'generate_ai_video' => ['nullable', 'boolean'],
            'media_processing' => ['nullable', 'in:original,enhance,advertisement,video'],
        ]);

        $event = Event::findOrFail($request->event_id);
        $caption = $request->caption;

        // Generate AI caption if requested and no caption provided
        if ($request->generate_ai_content && empty($caption)) {
            $caption = $this->generateAICaption($event, $request->type, $request->ai_prompt);
        }

        $post = EventPost::create([
            'event_id' => $request->event_id,
            'user_id' => Auth::id(),
            'type' => $request->type,
            'status' => 'draft',
            'caption' => $caption,
            'ai_prompt' => $request->ai_prompt,
        ]);

        // Handle media uploads
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $index => $mediaFile) {
                $path = $mediaFile->store('post-media', 'public');
                
                $mediaType = str_starts_with($mediaFile->getMimeType(), 'image/') ? 'image' : 'video';
                $source = 'upload';
                
                // Process media based on selected option
                if ($request->media_processing === 'video' && $mediaType === 'image') {
                    // Generate AI short video from image
                    $videoPath = $this->generateAIVideo($mediaFile, $event, $request->type);
                    if ($videoPath) {
                        $path = $videoPath;
                        $mediaType = 'video';
                        $source = 'ai_video';
                    }
                } elseif ($request->media_processing === 'enhance' && $mediaType === 'image') {
                    // AI enhance image
                    $enhancedPath = $this->enhanceImage($mediaFile, $event, $request->type);
                    if ($enhancedPath) {
                        $path = $enhancedPath;
                        $source = 'ai_enhanced';
                    }
                } elseif ($request->media_processing === 'advertisement' && $mediaType === 'image') {
                    // Generate advertisement-style content
                    $adPath = $this->generateAdvertisement($mediaFile, $event, $request->type);
                    if ($adPath) {
                        $path = $adPath;
                        $source = 'ai_advertisement';
                    }
                }
                
                $post->media()->create([
                    'path' => $path,
                    'type' => $mediaType,
                    'source' => $source,
                ]);
            }
        }

        // Generate AI video if requested without uploaded media
        if ($request->generate_ai_video && !$request->hasFile('media')) {
            $videoPath = $this->generateAIVideoFromEvent($event, $request->type);
            if ($videoPath) {
                $post->media()->create([
                    'path' => $videoPath,
                    'type' => 'video',
                    'source' => 'ai_video',
                ]);
            }
        }

        return redirect()
            ->route('multimedia.index')
            ->with('success', 'Post created successfully!');
    }

    private function generateAICaption(Event $event, string $postType, ?string $customPrompt = null): string
    {
        // Build context from event details
        $context = [
            'title' => $event->title,
            'description' => $event->description,
            'start_date' => $event->start_at->format('F j, Y'),
            'start_time' => $event->start_at->format('g:i A'),
            'venue' => $event->venue?->name,
            'status' => $event->status,
        ];

        // Generate caption based on post type
        $basePrompt = match($postType) {
            'invitation' => "Create an engaging invitation post for an event. Be enthusiastic and include key details.",
            'announcement' => "Create an exciting announcement post about an upcoming event. Build anticipation.",
            'highlight' => "Create a highlight post showcasing an amazing event that happened. Focus on energy and success.",
            'thank_you' => "Create a heartfelt thank you post to participants and supporters of an event. Show genuine appreciation.",
            'reminder' => "Create a friendly reminder post about an upcoming event. Create urgency without being pushy.",
            'advertisement' => "Create a compelling advertisement post to promote an event. Focus on benefits and excitement.",
            default => "Create an engaging social media post about an event.",
        };

        $prompt = $customPrompt ?: $basePrompt;
        $prompt .= "\n\nEvent Details:\n";
        $prompt .= "Title: {$context['title']}\n";
        $prompt .= "Date: {$context['start_date']}\n";
        $prompt .= "Time: {$context['start_time']}\n";
        if ($context['venue']) {
            $prompt .= "Venue: {$context['venue']}\n";
        }
        if ($context['description']) {
            $prompt .= "Description: {$context['description']}\n";
        }

        // For now, return a template-based caption
        // In the future, this would integrate with an AI service like OpenAI
        return $this->generateTemplateCaption($context, $postType);
    }

    private function generateTemplateCaption(array $context, string $postType): string
    {
        $templates = [
            'invitation' => [
                "🎉 You're invited to {$context['title']}! Join us on {$context['start_date']} at {$context['start_time']}" . 
                               ($context['venue'] ? " at {$context['venue']}" : "") . 
                               " for an amazing experience. Don't miss out! #Event #Invitation",
                "✨ Save the date! {$context['title']} is happening soon and we want YOU there! Join us for what promises to be an amazing day of connection, inspiration, and fun. Tag your friends! #SaveTheDate #EventInvitation",
                "🎊 Big news! We're thrilled to invite you to {$context['title']}! Prepare for an extraordinary event that will leave you inspired and energized. See you there! #BigEvent #YoureInvited"
            ],
            'announcement' => [
                "📢 Exciting news! We're thrilled to announce {$context['title']} happening on {$context['start_date']}. " .
                               "Get ready for an unforgettable experience. Mark your calendars! #Announcement #Event",
                "🌟 Amazing news everyone! {$context['title']} is officially happening! We've been working hard to bring you something truly special. Get ready for an experience like no other! #ComingSoon #EventAlert",
                "🎉 Drumroll please... We're excited to announce {$context['title']}! This is going to be HUGE! Prepare yourself for an amazing celebration you won't want to miss. #EventReveal #BigNews"
            ],
            'highlight' => [
                "✨ What an absolutely incredible time at {$context['title']}! The energy, the people, the moments - absolutely unforgettable. " .
                               "Here are some of our favorite moments from this spectacular event! #EventHighlights #AmazingMemories",
                "🌟 Still buzzing from the incredible energy at {$context['title']}! From start to finish, every moment was pure magic. Thank you to everyone who made this event absolutely unforgettable! #BestEventEver #Highlights",
                "🎊 {$context['title']} was absolutely EPIC! The vibes, the connections, the experiences - everything came together perfectly. Relive some of these incredible moments with us! #EventSuccess #Throwback"
            ],
            'thank_you' => [
                "🙏 Our hearts are overflowing with gratitude! To everyone who made {$context['title']} a massive success - our amazing participants, dedicated organizers, and incredible supporters - YOU are the real MVPs! Thank you for everything! #ThankYou #EventSuccess",
                "💝 Overwhelmed with appreciation! {$context['title']} was nothing short of magical, and that's all because of YOU! To our attendees, volunteers, and team - thank you for making dreams come true! #Grateful #CommunityLove",
                "🌟 Thank you, thank you, THANK YOU! {$context['title']} exceeded all expectations because of the incredible community that came together. We're still smiling from all the amazing moments! #Appreciation #EventFamily"
            ],
            'reminder' => [
                "⏰ Friendly reminder! {$context['title']} is just around the corner and we couldn't be more excited! Make sure you're all set for what promises to be an absolutely amazing experience. We can't wait to see you there! #EventReminder #GetReady",
                "🔔 Quick reminder! {$context['title']} is happening soon! Don't miss out on what's going to be an incredible day of fun, learning, and connection. Get ready to make some amazing memories! #DontForget #EventAlert",
                "⏰ Hey! {$context['title']} is coming up faster than we think! Time to get excited and prepared for what's going to be an absolutely fantastic event. See you there! #Reminder #EventVibes"
            ],
            'advertisement' => [
                "🎬 Get ready for {$context['title']}! This is more than just an event - it's an experience you don't want to miss. " .
                               "Join us on {$context['start_date']} and be part of something extraordinary! #Advertisement #DontMissOut",
                "🎥 Lights, camera, ACTION! {$context['title']} is coming and it's going to be absolutely incredible! This is your chance to be part of something truly special. Mark your calendars! #EventPromotion #MustAttend",
                "🎬 Short video created from uploaded content! Get ready to experience {$context['title']} like never before. " .
                               "This is more than just an event - it's going to be absolutely legendary! #VideoContent #EventHype"
            ]
        ];

        $typeTemplates = $templates[$postType] ?? $templates['announcement'];
        return $typeTemplates[array_rand($typeTemplates)];
    }

    private function generateAIVideo($imageFile, Event $event, string $postType): ?string
    {
        try {
            // Store the uploaded file temporarily
            $tempPath = $imageFile->store('temp', 'public');
            
            // Generate AI video options based on post type
            $options = [
                'prompt' => $this->getVideoPrompt($event, $postType),
                'duration' => rand(15, 30), // Random duration between 15-30 seconds
                'aspect_ratio' => '9:16', // Vertical video for social media
                'quality' => 'high',
            ];
            
            // Use AI service to generate video
            return $this->aiService->generateVideoFromImage($tempPath, $options);
        } catch (\Exception $e) {
            \Log::error('AI Video generation failed: ' . $e->getMessage());
            return null;
        }
    }

    private function generateAIVideoFromEvent(Event $event, string $postType): ?string
    {
        try {
            // Generate video options based on event details
            $options = [
                'prompt' => $this->getVideoPrompt($event, $postType),
                'duration' => rand(15, 30), // Random duration between 15-30 seconds
                'aspect_ratio' => '9:16', // Vertical video for social media
                'quality' => 'high',
            ];
            
            // Use AI service to generate video from text
            return $this->aiService->generateVideoFromText($options['prompt'], $options);
        } catch (\Exception $e) {
            \Log::error('AI Event Video generation failed: ' . $e->getMessage());
            return null;
        }
    }

    private function enhanceImage($imageFile, Event $event, string $postType): ?string
    {
        try {
            // Store the uploaded file temporarily
            $tempPath = $imageFile->store('temp', 'public');
            
            // Generate enhancement options based on post type
            $options = [
                'prompt' => $this->getEnhancePrompt($event, $postType),
                'scale' => 2,
            ];
            
            // Use AI service to enhance image
            return $this->aiService->enhanceImage($tempPath, $options);
        } catch (\Exception $e) {
            \Log::error('AI Image enhancement failed: ' . $e->getMessage());
            return null;
        }
    }

    private function generateAdvertisement($imageFile, Event $event, string $postType): ?string
    {
        try {
            // Store the uploaded file temporarily
            $tempPath = $imageFile->store('temp', 'public');
            
            // Generate advertisement options based on post type
            $options = [
                'prompt' => $this->getAdvertisementPrompt($event, $postType),
                'scale' => 1.5,
            ];
            
            // Use AI service to generate advertisement
            return $this->aiService->generateAdvertisement($tempPath, $options);
        } catch (\Exception $e) {
            \Log::error('AI Advertisement generation failed: ' . $e->getMessage());
            return null;
        }
    }

    private function getVideoPrompt(Event $event, string $postType): string
    {
        $prompts = [
            'invitation' => "Create an energetic invitation video for '{$event->title}'. Include smooth transitions and exciting visual effects. Make it feel welcoming and exclusive.",
            'announcement' => "Create a buzz-worthy announcement video for '{$event->title}'. Use dynamic text animations and build anticipation. Make it feel like big news.",
            'highlight' => "Create a stunning highlight video showcasing '{$event->title}'. Use epic transitions and emotional moments. Make it feel unforgettable and amazing.",
            'thank_you' => "Create a heartfelt thank you video for '{$event->title}'. Use warm colors and sincere messaging. Make it feel grateful and personal.",
            'reminder' => "Create an urgent reminder video for '{$event->title}'. Use attention-grabbing effects and clear call-to-action. Make it feel time-sensitive.",
            'advertisement' => "Create a viral-worthy advertisement video for '{$event->title}'. Use professional marketing visuals and compelling messaging. Make it irresistible and shareable.",
        ];

        return $prompts[$postType] ?? $prompts['announcement'];
    }

    private function getEnhancePrompt(Event $event, string $postType): string
    {
        $prompts = [
            'invitation' => "Enhance this invitation image for '{$event->title}'. Make colors vibrant and text more readable. Improve overall visual appeal.",
            'announcement' => "Enhance this announcement image for '{$event->title}'. Increase clarity, brightness, and make it more eye-catching.",
            'highlight' => "Enhance this highlight image from '{$event->title}'. Make colors pop and improve image quality for social media.",
            'thank_you' => "Enhance this thank you image for '{$event->title}'. Warm up the tones and improve emotional impact.",
            'reminder' => "Enhance this reminder image for '{$event->title}'. Make text clearer and colors more attention-grabbing.",
            'advertisement' => "Enhance this advertisement image for '{$event->title}'. Make it look professional and marketing-ready with vibrant colors.",
        ];

        return $prompts[$postType] ?? $prompts['announcement'];
    }

    private function getAdvertisementPrompt(Event $event, string $postType): string
    {
        $prompts = [
            'invitation' => "Transform this invitation image into a professional advertisement for '{$event->title}'. Add marketing elements and make it look premium.",
            'announcement' => "Transform this announcement image into a viral-worthy advertisement for '{$event->title}'. Use bold colors and marketing psychology.",
            'highlight' => "Transform this highlight image into an engaging advertisement for '{$event->title}'. Focus on excitement and FOMO.",
            'thank_you' => "Transform this thank you image into a brand-building advertisement for '{$event->title}'. Focus on community and appreciation.",
            'reminder' => "Transform this reminder image into an urgent advertisement for '{$event->title}'. Use scarcity and urgency tactics.",
            'advertisement' => "Create an explosive advertisement for '{$event->title}'. Use marketing psychology and make it irresistible.",
        ];

        return $prompts[$postType] ?? $prompts['advertisement'];
    }
}

