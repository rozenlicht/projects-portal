<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ResizeUserAvatars implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The maximum width and height for avatars.
     */
    private const MAX_DIMENSION = 150;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!extension_loaded('gd')) {
            Log::error('GD extension is not loaded. Cannot resize avatars.');
            throw new \RuntimeException('GD extension is required to resize images.');
        }

        $users = User::whereNotNull('avatar_url')->get();
        $resizedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        foreach ($users as $user) {
            try {
                if ($this->resizeAvatar($user)) {
                    $resizedCount++;
                } else {
                    $skippedCount++;
                }
            } catch (\Exception $e) {
                Log::error("Failed to resize avatar for user {$user->id}: {$e->getMessage()}");
                $errorCount++;
            }
        }

        Log::info("Avatar resize job completed. Resized: {$resizedCount}, Skipped: {$skippedCount}, Errors: {$errorCount}");
    }

    /**
     * Resize a user's avatar if it exceeds the maximum dimensions.
     *
     * @param  User  $user
     * @return bool  Returns true if the avatar was resized, false if it was already within limits
     */
    private function resizeAvatar(User $user): bool
    {
        $avatarPath = $user->avatar_url;

        if (!$avatarPath || !Storage::disk('public')->exists($avatarPath)) {
            Log::warning("Avatar file not found for user {$user->id}: {$avatarPath}");
            return false;
        }

        $fullPath = Storage::disk('public')->path($avatarPath);

        // Get image info
        $imageInfo = @getimagesize($fullPath);
        if ($imageInfo === false) {
            Log::warning("Invalid image file for user {$user->id}: {$avatarPath}");
            return false;
        }

        [$originalWidth, $originalHeight, $imageType] = $imageInfo;

        // Check if resize is needed
        if ($originalWidth <= self::MAX_DIMENSION && $originalHeight <= self::MAX_DIMENSION) {
            Log::info("Avatar for user {$user->id} is already within limits: {$originalWidth}x{$originalHeight}");
            return false; // Already within limits
        }

        // Calculate new dimensions maintaining aspect ratio
        $ratio = min(
            self::MAX_DIMENSION / $originalWidth,
            self::MAX_DIMENSION / $originalHeight
        );

        $newWidth = (int) round($originalWidth * $ratio);
        $newHeight = (int) round($originalHeight * $ratio);

        // Create image resource from file
        $sourceImage = $this->createImageFromFile($fullPath, $imageType);
        if ($sourceImage === false) {
            throw new \RuntimeException("Failed to create image resource from file: {$avatarPath}");
        }

        // Create new image with calculated dimensions
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG and GIF
        if ($imageType === IMAGETYPE_PNG || $imageType === IMAGETYPE_GIF) {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Resize image
        imagecopyresampled(
            $newImage,
            $sourceImage,
            0, 0, 0, 0,
            $newWidth,
            $newHeight,
            $originalWidth,
            $originalHeight
        );

        // Save resized image
        $this->saveImage($newImage, $fullPath, $imageType);

        // Clean up memory
        imagedestroy($sourceImage);
        imagedestroy($newImage);

        Log::info("Resized avatar for user {$user->id} from {$originalWidth}x{$originalHeight} to {$newWidth}x{$newHeight}");

        return true;
    }

    /**
     * Create an image resource from a file based on its type.
     *
     * @param  string  $filePath
     * @param  int  $imageType
     * @return resource|false
     */
    private function createImageFromFile(string $filePath, int $imageType)
    {
        return match ($imageType) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($filePath),
            IMAGETYPE_PNG => imagecreatefrompng($filePath),
            IMAGETYPE_GIF => imagecreatefromgif($filePath),
            IMAGETYPE_WEBP => imagecreatefromwebp($filePath),
            default => false,
        };
    }

    /**
     * Save an image resource to a file.
     *
     * @param  resource  $image
     * @param  string  $filePath
     * @param  int  $imageType
     * @return void
     */
    private function saveImage($image, string $filePath, int $imageType): void
    {
        $success = match ($imageType) {
            IMAGETYPE_JPEG => imagejpeg($image, $filePath, 90),
            IMAGETYPE_PNG => imagepng($image, $filePath, 9),
            IMAGETYPE_GIF => imagegif($image, $filePath),
            IMAGETYPE_WEBP => imagewebp($image, $filePath, 90),
            default => false,
        };

        if ($success === false) {
            throw new \RuntimeException("Failed to save resized image to: {$filePath}");
        }
    }
}

