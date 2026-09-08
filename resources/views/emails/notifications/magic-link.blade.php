<x-mail::message>
# Sign in to {{ config('app.name') }}

{{ $notification->message }}

If you didn't request this link, you can safely ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
