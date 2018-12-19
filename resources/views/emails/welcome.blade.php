<div>
    <p>Dear Ms./Mr. {{$name}},</p>
    <p>Welcome to our restaurant, you have been registered successfully as {{$type}}.</p>
    <p>To finalize the registration, please confirm your account <a href="{{url('user/verify', $token)}}">here</a>
</div>