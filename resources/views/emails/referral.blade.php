@component('mail::message')
# Hello,

<p>You have been invited by {{$name}} to to join {{$organization}} on Nzukoor.
  </p>
  <p> A Social Learning Place, we hope you like it here.</p>

@component('mail::button', ['url' => $url])
Click to get started
@endcomponent

<div>or</div>
<div>Use this referral code {{$code}} to create an account</div>


<small>
Thanks,<br>
{{ config('app.name') }}
</small>
@endcomponent
