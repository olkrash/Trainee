@component('mail::message')

You have requested the password reset, use your token to reset it.
Token: <b>{{$token}}</b>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
