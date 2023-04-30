UserCreated

Welcome {{$user->name}}!
Please verify your email address:

{{ route('verify', $user->verification_token ) }}


