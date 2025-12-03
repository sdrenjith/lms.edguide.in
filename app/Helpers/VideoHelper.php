<?php

namespace App\Helpers;

class VideoHelper
{
    /**
     * Extract YouTube video ID from various YouTube URL formats
     */
    public static function getYoutubeVideoId($url)
    {
        if (empty($url)) {
            return null;
        }

        $patterns = [
            // YouTube Shorts
            '/^.*(?:youtube\.com\/shorts\/)([^#\&\?]*).*/',
            // Standard YouTube watch URLs
            '/^.*(?:youtu\.be\/|v\/|e\/|u\/\w+\/|embed\/|watch\?v=|&v=)([^#\&\?]*).*/',
            // YouTube embed URLs
            '/^.*(?:youtube\.com\/embed\/)([^#\&\?]*).*/',
            // YouTube v URLs
            '/^.*(?:youtube\.com\/v\/)([^#\&\?]*).*/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                if (isset($matches[1]) && strlen($matches[1]) === 11) {
                    return $matches[1];
                }
            }
        }

        return null;
    }

    /**
     * Check if a URL is a valid YouTube URL
     */
    public static function isValidYoutubeUrl($url)
    {
        return self::getYoutubeVideoId($url) !== null;
    }

    /**
     * Generate YouTube embed URL
     */
    public static function getYoutubeEmbedUrl($url)
    {
        $videoId = self::getYoutubeVideoId($url);
        if ($videoId) {
            return "https://www.youtube.com/embed/{$videoId}?rel=0";
        }
        return null;
    }

    /**
     * Get video type (file or youtube)
     */
    public static function getVideoType($video)
    {
        if ($video->youtube_url) {
            return 'youtube';
        }
        if ($video->video_path) {
            return 'file';
        }
        return null;
    }
} 