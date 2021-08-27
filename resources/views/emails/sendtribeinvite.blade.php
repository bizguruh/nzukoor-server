@component('mail::message')
# {{$greeting}}

{{$body}}

@component('mail::button', ['url' => $url])
{{$actionText}}
@endcomponent



<small>
  Thanks,<br>
{{ $sender }}
</small>
@endcomponent
