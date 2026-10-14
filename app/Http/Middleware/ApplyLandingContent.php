<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menimpa nilai bawaan config/retina.php dengan konten yang disimpan admin
 * lewat halaman "Konten Landing Page", sehingga view cukup membaca config().
 */
class ApplyLandingContent
{
    /**
     * Kolom yang bisa diubah admin => kunci config yang ditimpanya.
     *
     * @var array<string, string>
     */
    public const FIELDS = [
        'speaker_role' => 'retina.speaker.role',
        'speaker_bio' => 'retina.speaker.bio',
        'speaker_photo' => 'retina.speaker.photo',
        'sessions' => 'retina.sessions',
        'perks' => 'retina.perks',
        'contact_name' => 'retina.contact.name',
        'contact_whatsapp' => 'retina.contact.whatsapp',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        config(static::overrides(Setting::getJson(Setting::LANDING_CONTENT)));

        return $next($request);
    }

    /**
     * Hanya kolom yang pernah disimpan admin yang menimpa config; sisanya tetap bawaan.
     *
     * @param  array<string, mixed>  $content
     * @return array<string, mixed>
     */
    public static function overrides(array $content): array
    {
        $overrides = [];

        foreach (self::FIELDS as $field => $configKey) {
            if (array_key_exists($field, $content)) {
                $overrides[$configKey] = $content[$field];
            }
        }

        if (array_key_exists('ends_at_time', $content)) {
            $overrides['retina.ends_at'] = filled($content['ends_at_time'])
                ? Carbon::parse(config('retina.starts_at'))->format('Y-m-d').' '.$content['ends_at_time']
                : null;
        }

        return $overrides;
    }
}
