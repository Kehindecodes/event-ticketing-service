<x-mail::message>
# Update on your payment

{{ $notification->message }}

If you have any questions about this charge, just reply to this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
