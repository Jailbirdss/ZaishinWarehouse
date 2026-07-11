<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Login - Zaishin Warehouse</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css?v=<?= time() ?>"/>
</head>
<body>
<div class="login-page">

  <div class="login-left">
    <div class="login-left-content">
      <div class="login-logo-icon">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 7l9-4 9 4v13H3V7z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 21V12h6v9"/>
        </svg>
      </div>
      <h1 class="login-left-title">Zaishin Warehouse</h1>
      <p class="login-left-subtitle">Warehouse Management System</p>
      <div class="login-left-desc">
        Sistem manajemen pergudangan modern untuk bisnis percetakan. Kelola inventaris, pergerakan stok, dan peta gudang dalam satu platform terintegrasi.
      </div>
    </div>
  </div>

  <div class="login-right">
    <div class="login-form-container">
      <div class="mb-7">
        <h2 class="fs-5xl fw-800 text-main mb-2">Selamat Datang</h2>
        <p class="fs-base text-muted">Masuk ke akun Anda untuk melanjutkan</p>
      </div>

      <?php if (!empty($error)): ?>
      <div class="alert alert-danger mb-5">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span><?= htmlspecialchars($error) ?></span>
      </div>
      <?php endif; ?>

      <form method="POST" action="index.php?page=login" id="login-form">
        <div class="form-group">
          <label class="form-label" for="username">Username</label>
          <div class="pos-relative">
            <svg class="form-icon-left"
              fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <input id="username" name="username" type="text" class="form-control input-with-icon-left"
              placeholder="Masukkan username"
              value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autofocus/>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <div class="pos-relative">
            <svg class="form-icon-left"
              fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <input id="password" name="password" type="password" class="form-control input-with-icon-both"
              placeholder="Masukkan password" required/>
            <button type="button" id="toggle-pw" class="form-icon-right">
              <svg id="eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-auto h-auto">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="remember-me-wrapper">
          <label class="remember-me-container">
            <input type="checkbox" name="remember" id="remember" class="remember-checkbox-hidden"/>
            <span class="remember-checkbox-custom">
              <svg class="remember-checkmark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
              </svg>
            </span>
            <span class="fs-13">Ingat saya (30 hari)</span>
          </label>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-full justify-center mt-2">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-auto h-auto">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
          </svg>
          Masuk
        </button>
      </form>

      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('toggle-pw').addEventListener('click', () => {
  const pw  = document.getElementById('password');
  const ico = document.getElementById('eye-icon');
  if (pw.type === 'password') {
    pw.type = 'text';
    ico.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`;
  } else {
    pw.type = 'password';
    ico.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
  }
});
</script>
</body>
</html>
