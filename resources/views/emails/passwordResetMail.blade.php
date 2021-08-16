@component('mail::message')

Use the link to reset your password

@component('mail::button', ['url' => $maildata['url']])
Reset Link
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent