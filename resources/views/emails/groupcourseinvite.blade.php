@component('mail::message')
# {{$greeting}}

{{$body}}

@component('mail::button', ['url' => $url])
{{$actionText}}
@endcomponent

{{$url}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
