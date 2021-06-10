@component('mail::message')
# NEW MESSAGE

{{$from_name}} <br>
{{$from_email}} <br>
{{$body}}



Thanks,<br>
{{ config('app.name') }}
@endcomponent
