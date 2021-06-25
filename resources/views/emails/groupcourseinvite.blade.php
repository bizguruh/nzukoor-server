@component('mail::message')
# {{$greeting}}

{{$body}}

@component('mail::button', ['url' => $url])
{{$actionText}}
@endcomponent

<small>{{$url}}</small> <br>
@if($code)
<small>Or use the code, {{$code}} to register</small>
@endif

Thanks,<br>
{{ $sender }}
@endcomponent
