@component('mail::message')
# {{$greeting}}

{{$body}}

@component('mail::button', ['url' => $url])
{{$actionText}}
@endcomponent

<small>{{$url}}</small>


<small>
Thanks,<br>
{{ config('app.name') }}
</small>
@endcomponent
