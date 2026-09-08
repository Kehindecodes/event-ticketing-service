<x-mail::message>
# Your ticket offer has expired

Hi {{ $user->name }},

{{ $notification->message }}

Keep an eye out — new tickets can open up as other holds expire.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
