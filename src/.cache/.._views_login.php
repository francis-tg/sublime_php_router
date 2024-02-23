<?php class_exists('Francis\SublimePhp\Engine\View') or exit; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-info">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Navbar</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            
        </div>
    </div>
</nav>
    
<div class="container w-100">
    <div class="m-auto">
        <div class="col-md-6">
            <h2>
                Login
            </h2>
            <form action="" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control" id="email" aria-describedby="emailHelp">
                    <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
                </div>
                <div class="mb-3">
                  <label for="password" class="form-label">Password</label>
                  <input type="password"
                    class="form-control" name="password" id="password" aria-describedby="helpId">
                  <small id="helpId" class="form-text text-muted"></small>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <span>
                Vous n'avez pas de compte ? <a href="/register">créez en un</a>
            </span>
        </div>
    </div>
</div>

</body>

</html>

