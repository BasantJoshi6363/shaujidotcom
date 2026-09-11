<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | ShaujiDotCom</title>
</head>
<body>
    <h1>Login</h1>
    <form action="{{ route('login.post') }}" method="post">
        @csrf
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <a href="{{ route('google.redirect') }}">login with google</a>
        <a href="{{ route('facebook.redirect') }}">login with facebook</a>

        <a href="/register">don't have an account? Register here</a>

        <a href="{{ route('forget-password') }}">Forgot Password?</a>
    </form>
</body>
</html>