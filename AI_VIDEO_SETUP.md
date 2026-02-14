# AI Video Generation Setup

This document explains how to set up AI video generation for your EMS multimedia posts.

## Supported AI Services

### Free Tier Services (Recommended)

1. **RunwayML** 
   - Free tier: 100 credits/month
   - Max duration: 30 seconds
   - Sign up: https://runwayml.com/

2. **Pika Labs**
   - Free tier: 150 credits/month  
   - Max duration: 15 seconds
   - Sign up: https://pika.art/

3. **Stable Video Diffusion**
   - Free tier: 100 credits/month
   - Max duration: 20 seconds
   - Sign up: https://stability.ai/

4. **Kaiber**
   - Free tier: 30 credits/month
   - Max duration: 30 seconds
   - Sign up: https://kaiber.ai/

## Configuration

### 1. Add API Keys to .env

```bash
# RunwayML (Recommended)
RUNWAYML_API_KEY=your_runwayml_api_key_here

# Pika Labs
PIKA_API_KEY=your_pika_api_key_here

# Stable Video Diffusion
STABLE_VIDEO_API_KEY=your_stable_video_api_key_here

# Kaiber
KAIBER_API_KEY=your_kaiber_api_key_here

# Replicate (for image enhancement)
REPLICATE_API_KEY=your_replicate_api_key_here

# FAL (alternative for image enhancement)
FAL_API_KEY=your_fal_api_key_here
```

### 2. Configure Default Service

In `config/ai_services.php`, change the default service:

```php
'default' => 'runwayml', // or 'pika', 'stable_video', 'kaiber'
```

### 3. FFmpeg Setup (Optional)

For fallback video generation, install FFmpeg:

**Windows:**
```bash
# Using Chocolatey
choco install ffmpeg

# Or download from https://ffmpeg.org/download.html
# Add to PATH: C:\Program Files\ffmpeg\bin
```

**Linux:**
```bash
sudo apt update && sudo apt install ffmpeg
```

**macOS:**
```bash
brew install ffmpeg
```

Update the FFmpeg path in `.env`:
```bash
FFMPEG_PATH=ffmpeg
```

## Usage

### Creating Posts with AI Videos

1. **Upload an image** and select "Short Video" processing option
2. **Enable "Generate AI short video"** checkbox
3. **Choose post type** - each type has optimized prompts
4. **Add custom AI instructions** (optional)
5. **Create post** - AI will generate a 15-30 second video

### Video Generation Features

- **Random Duration**: Videos are 15-30 seconds for variety
- **Vertical Format**: Optimized for social media (9:16 aspect ratio)
- **Dynamic Prompts**: Each post type has specialized prompts
- **Fallback Support**: FFmpeg fallback if AI services fail
- **Multiple Services**: Switch between AI providers easily

### Post Type Prompts

- **Invitation**: Energetic, welcoming, exclusive feel
- **Announcement**: Buzz-worthy, news-breaking style
- **Highlight**: Epic, emotional, unforgettable moments
- **Thank You**: Heartfelt, grateful, sincere tone
- **Reminder**: Urgent, attention-grabbing, time-sensitive
- **Advertisement**: Viral-worthy, professional, marketing-focused

### Dynamic Success Messages

Each post type shows unique success messages:

- **With Video**: "🎬 Invitation video created! Your guests will love this cinematic invitation!"
- **Without Video**: "📧 Invitation created successfully!"

## Troubleshooting

### Videos Not Generating

1. Check API keys in `.env`
2. Verify service credits
3. Check FFmpeg installation (for fallback)
4. Review logs: `php artisan log:tail`

### Common Issues

- **API Rate Limits**: Free tiers have monthly limits
- **Video Duration**: Some services limit to 15 seconds
- **File Size**: Large images may timeout
- **Network**: AI services require internet connection

## Production Deployment

1. Set up monitoring for AI service usage
2. Configure error handling and logging
3. Test with various image types and sizes
4. Monitor credit consumption
5. Set up alerts for service outages

## Security Notes

- API keys are stored in environment variables
- All AI requests are logged
- Temporary files are cleaned up automatically
- Input validation prevents malicious uploads

## Future Enhancements

- Real-time video generation progress
- Custom video templates
- Batch video generation
- Video editing capabilities
- Integration with more AI services
