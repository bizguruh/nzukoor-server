@component('mail::message')
    # Password reset

    You have requested a password change, use the OTP code below

    <h3>{{ $code }}</h3>

    @component('mail::button', ['url' => ''])
        Button Text
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
