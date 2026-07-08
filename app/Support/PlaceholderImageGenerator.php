<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class PlaceholderImageGenerator
{
    protected const WIDTH = 800;

    protected const HEIGHT = 1000;

    protected const WORK_WIDTH = 400;

    protected const WORK_HEIGHT = 500;

    protected const PALETTES = [
        'from-neutral-300 to-neutral-400' => ['top' => [230, 230, 229], 'bottom' => [180, 179, 177], 'accent' => [140, 138, 135]],
        'from-neutral-200 to-neutral-400' => ['top' => [238, 238, 237], 'bottom' => [185, 184, 182], 'accent' => [150, 148, 145]],
        'from-sky-100 to-neutral-300' => ['top' => [232, 244, 252], 'bottom' => [205, 213, 216], 'accent' => [150, 178, 196]],
        'from-stone-200 to-stone-400' => ['top' => [235, 232, 229], 'bottom' => [186, 179, 173], 'accent' => [156, 144, 132]],
        'from-neutral-300 to-neutral-500' => ['top' => [222, 222, 221], 'bottom' => [140, 139, 137], 'accent' => [110, 108, 105]],
        'from-emerald-900/30 to-neutral-400' => ['top' => [214, 226, 217], 'bottom' => [184, 190, 184], 'accent' => [104, 138, 116]],
    ];

    /**
     * Generate a soft, abstract studio-style placeholder photo for a product and store it
     * on the public disk. This stands in for real product photography, which we don't have.
     *
     * No caption is baked into the image — product cards render the name as real text, and
     * a burned-in label would get cropped unpredictably across the different aspect ratios
     * (square teasers, 4:5 shop cards, 3:4 lookbook cards) product photos are shown at.
     */
    public static function generate(string $slug, string $gradient): string
    {
        $palette = self::PALETTES[$gradient] ?? self::PALETTES['from-neutral-300 to-neutral-400'];

        $work = self::paintBackground($palette);
        self::paintGlow($work, $palette);

        imagefilter($work, IMG_FILTER_GAUSSIAN_BLUR);
        imagefilter($work, IMG_FILTER_GAUSSIAN_BLUR);
        imagefilter($work, IMG_FILTER_GAUSSIAN_BLUR);

        $final = imagecreatetruecolor(self::WIDTH, self::HEIGHT);
        imagecopyresampled(
            $final, $work,
            0, 0, 0, 0,
            self::WIDTH, self::HEIGHT, self::WORK_WIDTH, self::WORK_HEIGHT
        );

        $path = "products/{$slug}.jpg";

        ob_start();
        imagejpeg($final, null, 90);
        $contents = ob_get_clean();

        Storage::disk('supabase')->put($path, $contents);

        return $path;
    }

    /**
     * Generate a mock bank-transfer receipt image used to back seeded demo orders, so seeding
     * doesn't depend on a binary fixture file being present on disk.
     */
    public static function generateSampleReceipt(): string
    {
        $width = 600;
        $height = 800;

        $image = imagecreatetruecolor($width, $height);
        $background = imagecolorallocate($image, 245, 245, 244);
        $foreground = imagecolorallocate($image, 60, 60, 60);

        imagefilledrectangle($image, 0, 0, $width, $height, $background);
        imagerectangle($image, 20, 20, $width - 20, $height - 20, $foreground);

        $lines = [
            'BANK TRANSFER RECEIPT', '',
            'Bank: BCA', 'To: PT Embodied Studio', 'Acc: 1234567890', '',
            'Status: SUCCESS', '', '(sample placeholder proof)',
        ];

        $y = 100;
        foreach ($lines as $line) {
            imagestring($image, 4, 60, $y, $line, $foreground);
            $y += 30;
        }

        $path = 'payment-proofs/sample.jpg';

        ob_start();
        imagejpeg($image, null, 85);
        $contents = ob_get_clean();

        Storage::disk('supabase')->put($path, $contents);

        return $path;
    }

    /**
     * @param  array{top: int[], bottom: int[], accent: int[]}  $palette
     * @return \GdImage
     */
    protected static function paintBackground(array $palette)
    {
        $width = self::WORK_WIDTH;
        $height = self::WORK_HEIGHT;

        $image = imagecreatetruecolor($width, $height);
        $diagonal = $width + $height;

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $ratio = ($x + $y) / $diagonal;
                $r = (int) ($palette['top'][0] + ($palette['bottom'][0] - $palette['top'][0]) * $ratio);
                $g = (int) ($palette['top'][1] + ($palette['bottom'][1] - $palette['top'][1]) * $ratio);
                $b = (int) ($palette['top'][2] + ($palette['bottom'][2] - $palette['top'][2]) * $ratio);
                imagesetpixel($image, $x, $y, imagecolorallocate($image, $r, $g, $b));
            }
        }

        return $image;
    }

    /**
     * @param  \GdImage  $image
     * @param  array{top: int[], bottom: int[], accent: int[]}  $palette
     */
    protected static function paintGlow($image, array $palette): void
    {
        $width = self::WORK_WIDTH;
        $height = self::WORK_HEIGHT;

        $glow = imagecolorallocatealpha($image, ...[...$palette['accent'], 105]);
        imagefilledellipse($image, (int) ($width * 0.38), (int) ($height * 0.36), (int) ($width * 0.85), (int) ($height * 0.6), $glow);

        $glowSoft = imagecolorallocatealpha($image, ...[...$palette['top'], 110]);
        imagefilledellipse($image, (int) ($width * 0.68), (int) ($height * 0.68), (int) ($width * 0.7), (int) ($height * 0.5), $glowSoft);
    }
}
