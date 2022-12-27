@component('mail::message')
# Hello,

You have been invited to join the crew of
**{{ $invite->crew->name }}**.
Because you are not yet signed up to the platform, please
[Register for free]({{ $url }}), then you can accept or reject the invite in your crew management console.

@component('mail::button', ['url' => $url])
REGISTER
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
