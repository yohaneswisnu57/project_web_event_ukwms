<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function __invoke(): View
    {
        $startsAt = Carbon::parse(config('retina.starts_at'));
        $endsAt = filled(config('retina.ends_at')) ? Carbon::parse(config('retina.ends_at')) : null;

        return view('welcome', [
            'startsAt' => $startsAt,
            'endsAt' => $endsAt,
            'isRegistrationOpen' => Participant::isRegistrationOpen(),
            'registrationClosesAt' => Participant::registrationClosesAt(),
            'remainingQuota' => Participant::remainingQuota(),
            'calendarUrl' => $this->googleCalendarUrl($startsAt, $endsAt ?? $startsAt),
        ]);
    }

    protected function googleCalendarUrl(Carbon $startsAt, Carbon $endsAt): string
    {
        $format = fn (Carbon $time): string => $time->copy()->utc()->format('Ymd\THis\Z');

        return 'https://calendar.google.com/calendar/render?'.http_build_query([
            'action' => 'TEMPLATE',
            'text' => config('retina.name').' — '.config('retina.tagline'),
            'dates' => $format($startsAt).'/'.$format($endsAt),
            'location' => implode(', ', [
                config('retina.venue.room'),
                config('retina.venue.building'),
                config('retina.venue.campus'),
                config('retina.venue.address'),
            ]),
            'details' => config('retina.theme'),
        ]);
    }
}
