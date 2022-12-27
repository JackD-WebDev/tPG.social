@component('mail::message')
# Hi,

You have been invited to join the crew of
**{{ $invite->crew->name }}**.
Because you are already registered to the platform, you just need to accept or reject the invitation in your
[crew management console]({{ $url }}).

@component('mail::button', ['url' => $url])
GO TO DASHBOARD
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
