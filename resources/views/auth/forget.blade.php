<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password | ShaujiDotCom</title>
</head>
<body>
    <h1>Forget Password</h1>

    <form action="/reset" method="post">
        @csrf
        <p>Enter your email address to reset your password.</p>
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>