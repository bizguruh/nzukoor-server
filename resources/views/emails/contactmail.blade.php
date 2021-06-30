@component('mail::message')
# NEW MESSAGE

{{$from_name}} <br>
{{$from_email}} <br>
{{$body}}




<small>
Thanks,<br>
{{ config('app.name') }}
</small>
@endcomponent
