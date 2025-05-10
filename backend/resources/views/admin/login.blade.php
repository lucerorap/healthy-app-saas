<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Admin Login</title>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row my-5">
            <div class="col-md-4 mx-auto">
                <main class="form-signin w-100 mt-5">
                    <form class="text-center" method="POST" action="{{route('admin.auth')}}">
                        @csrf
                        <img class="mb-4 rounded" src="https://cdn.pixabay.com/photo/2021/01/18/16/02/avocado-5928508_1280.png" alt="logo" width="72" height="57">
                        <h1 class="h3 mb-3 fw-normal">Please sign in</h1>
                        <div class="form-floating mb-3">
                            <input type="email" name="email"
                            class="form-control @error('email') is-invalid @enderror" 
                            id="floatingInput" placeholder="name@example.com">
                            <label for="floatingInput">Email address</label>
                            @error('email')
                                <div class="invalid-feedback">
                                    <strong>
                                        {{ $message }}
                                    </strong>
                                </div>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            id="floatingPassword" placeholder="Password">
                            <label for="floatingPassword">Password</label>
                            @error('password')
                                <div class="invalid-feedback">
                                    <strong>
                                        {{ $message }}
                                    </strong>
                                </div>
                            @enderror
                        </div>
                        <button class="btn btn-dark w-100 py-2" type="submit">Sign in</button>
                        <p class="mt-5 mb-3 text-body-secondary">&copy;{{ \Carbon\Carbon::now()->year }}</p>
                    </form>
                </main>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>