<x-mail::message>
# You're going! 🎟️

Hi {{ $user->name }},

{{ $notification->message }}

We'll see you there.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
