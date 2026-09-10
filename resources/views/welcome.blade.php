<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=\, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>Welcome</h1> {{ session('success') }}
    {{ Auth::user()->name }}

    <form action="/logout" method="post">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>

</html>