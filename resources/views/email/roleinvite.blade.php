@component('mail::message')
{{-- Greeting --}}
@if ( empty($greeting))
 Dear {{$username}}
@endif

{{-- Action Button --}}


<div>

<p>You have been invited by {{$organization}} to be a {{$role}} on Nzukoor.
  </p>
  <p> Welcome to The Social Learning Place, we hope you like it here.</p>
<p>Please log in to get started.</p>
<p>Email: {{$email}}</p>
<p>Password: {{$password}}</p>
<a href="http://nzukoor.com/login"><button class="button"></button></a>

@component('mail::button', ['url' => 'http://nzukoor.com/login', 'color' => 'primary'])
Click to login
@endcomponent


</div>

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
@lang('Thank you for using our platform') <br>
<p>The Team @ Nzukoor</p>

@endif

{{-- Subcopy --}}
@isset($actionText)
@slot('subcopy')
@lang(
    "If you’re having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser:',
    [
        'actionText' => $actionText,
    ]
) <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
@endslot
@endisset
@endcomponent

