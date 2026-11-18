<?php

namespace App\Support;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class Qr
{
    public static function svg(string $text, int $size = 180): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size, 1),
            new SvgImageBackEnd()
        );
        $svg = (new Writer($renderer))->writeString($text);

        return preg_replace('/^<\?xml.*?\?>\s*/', '', $svg);
    }
}
