<x-mail::message>
# Your ticket has been offered!

Hi {{ $user->name }},

{{ $notification->message }}

Please confirm as soon as possible — ticket offers don't stay open forever.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
