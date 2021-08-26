<form action="{{ route('register.attempt') }}" method="post">
    @csrf
    <input type="text" name="name" value="{{ old('name') }}" placeholder="Name">
    <input type="email" name="email" value="{{ old('email') }}" placeholder="Email">
    <input type="password" name="password" value="{{ old('password') }}" placeholder="Password">
    <input type="password" name="password_confirmation" value="{{ old('password_confirmation') }}" placeholder="Password Confirm">
    <button type="submit">
        Register
    </button>
</form>
