<?php 
$pageTitle = 'Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .login-card { border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); overflow: hidden; }
        .login-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; }
        .login-header h3 { color: white; margin: 0; font-weight: bold; }
        .login-header p { color: rgba(255,255,255,0.8); margin: 10px 0 0; }
        .login-body { padding: 40px; background: white; }
        .btn-login { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 12px; font-weight: bold; }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(102,126,234,0.4); }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5">
                <div class="login-card">
                    <div class="login-header">
                        <h3><i class="fas fa-clinic-medical"></i> <?= APP_NAME ?></h3>
                        <p>Clinic Management System</p>
                    </div>
                    <div class="login-body">
                        <?php if(isset($_SESSION['flash'])): ?>
                            <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show">
                                <?= htmlspecialchars($_SESSION['flash']['message']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['flash']); ?>
                        <?php endif; ?>
                        
                        <form method="POST" action="index.php?page=login">
                            <?= CSRF::getTokenField() ?>
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required autofocus>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 btn-login">
                                <i class="fas fa-sign-in-alt"></i> Sign In
                            </button>
                        </form>
                        <hr>
                        <div class="text-center text-muted small">
                            <p>Demo Accounts:</p>
                            <p>Admin: admin@clinic.local / Admin@1234</p>
                            <p>Doctor: ahmed@clinic.local / Admin@1234</p>
                            <p>Patient: sara@example.com / Admin@1234</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>