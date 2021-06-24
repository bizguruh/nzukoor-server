@component('mail::message')
# {{$greeting}}

{{$body}}

@component('mail::button', ['url' => $url])
{{$actionText}}
@endcomponent

<small>{{$url}}</small> <br>
<small>Or use the code, {{$code}} to register</small>

Thanks,<br>
{{ $sender }}
@endcomponent
