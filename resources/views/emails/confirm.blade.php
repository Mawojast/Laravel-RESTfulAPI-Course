UserUpdated

Hello {{$user->name}}!
You changed your email.
Please verify your email address:

{{ route('verify', $user->verification_token ) }}
