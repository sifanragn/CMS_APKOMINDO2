<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login - MGBK</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
body {
  margin: 0;
  min-height: 100vh;
  font-family: 'Segoe UI', sans-serif;
  background: 
    linear-gradient(
      rgba(13, 71, 161, 0.75),
      rgba(13, 71, 161, 0.75)
    ),
    url("{{ asset('storage/munas-apkomindo.jpg') }}") center/cover no-repeat;
  overflow: hidden;
}

/* Wrapper Card */
.card-login {
  border-radius: 22px;
  background: #fff;
  padding: 3rem 2rem 2.2rem;
  width: 420px;
  position: relative;
  padding-top: 4rem;
  box-shadow:
    0 30px 60px rgba(13, 71, 161, 0.35),
    inset 0 0 0 1px rgba(13, 71, 161, 0.05);
}

/* Accent top */
.card-login::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  height: 6px;
  width: 100%;
  border-radius: 22px 22px 0 0;
}

/* Logo */
.logo-box {
  width: 90px;
  height: 90px;
  position: absolute;
  top: -45px;
  left: 50%;
  transform: translateX(-50%);
  background: white;
  border-radius: 50%;
  box-shadow: 0 10px 25px rgba(0,0,0,0.15);
  display: flex;
  align-items: center;
  justify-content: center;
}


.text-primary-apkomindo {
  color: #0d47a1;
}

/* Input */
.form-control {
  border-radius: 12px;
  padding: 12px 14px;
  background: #f4f7fb;
  border: 1px solid #e0e6ed;
}

.form-control:focus {
  background: #fff;
  border-color: #0d47a1;
  box-shadow: 0 0 0 0.2rem rgba(13, 71, 161, 0.15);
}

/* Button */
.btn-apkomindo {
  background: linear-gradient(135deg, #d32f2f, #b71c1c);
  border: none;
  border-radius: 14px;
  font-weight: 600;
  letter-spacing: 0.5px;
  padding: 12px;
  transition: all .3s ease;
}

.btn-apkomindo:hover {
  transform: translateY(-1px);
  box-shadow: 0 10px 20px rgba(211,47,47,0.4);
}

/* Wave */
.wave {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  z-index: 1;
}
</style>


</head>
<body>
  <!-- Login Wrapper -->
    
    <div class="d-flex justify-content-center align-items-center vh-100 position-relative" style="z-index:2;">
    
    <div class="card-login text-center">

      <div class="logo-box">
        <img src="{{ asset('storage/logo.png') }}" width="55">
      </div>

      <h4 class="fw-bold text-primary-apkomindo">APKOMINDO</h4>
      <small class="text-muted d-block mb-4">
        Asosiasi Pengusaha Komputer Indonesia
      </small>

      <h5 class="fw-semibold mb-4">Silakan Login</h5>

      <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3 text-start">
          <label class="fw-semibold mb-1">Email</label>
          <input type="email" name="email" class="form-control" required autofocus>
        </div>

        <div class="mb-4 text-start">
          <label class="fw-semibold mb-1">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>

        <button class="btn btn-apkomindo text-white w-100">
          Login
        </button>
      </form>

    </div>

  </div>



  <!-- Notifikasi SweetAlert -->
  @if (session('status'))
  <script>
    Swal.fire({
      icon: 'success',
      title: 'Success',
      text: "{{ session('status') }}",
      confirmButtonText: 'OK'
    });
  </script>
  @endif

  @if ($errors->any())
  <script>
    Swal.fire({
      icon: 'error',
      title: 'Login Gagal',
      html: `{!! implode('<br>', $errors->all()) !!}`,
      confirmButtonText: 'Coba Lagi'
    });
  </script>
  @endif
</body>
</html>
