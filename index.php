<?php
    session_start();
    // Redirect to dashboard if already logged in
    if(isset($_SESSION['user_id'])) {
        echo "<script>window.location.href = 'dashboard.php';</script>";
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Microsoft Copilot Clone | Login</title>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f0f2f5;
            color: #2c2c2c;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            padding: 0 20px;
            margin: 0 auto;
        }

        .auth-container {
            display: flex;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            min-height: 550px;
        }

        .auth-image {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0078d4, #106ebe);
            position: relative;
            overflow: hidden;
            padding: 40px;
            display: none;
        }

        @media (min-width: 768px) {
            .auth-image {
                display: flex;
            }
        }

        .auth-image-content {
            color: white;
            z-index: 2;
            text-align: center;
        }

        .auth-image-content h2 {
            font-size: 28px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .auth-image-content p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .auth-image::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -120px;
            right: -120px;
        }

        .auth-image::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            bottom: -80px;
            left: -80px;
        }

        .auth-form {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo {
            margin-bottom: 30px;
            display: flex;
            align-items: center;
        }

        .logo svg {
            height: 32px;
            margin-right: 10px;
        }

        .logo h1 {
            font-size: 24px;
            font-weight: 600;
            color: #0078d4;
        }

        .form-title {
            margin-bottom: 20px;
        }

        .form-title h2 {
            font-size: 24px;
            font-weight: 600;
            color: #2c2c2c;
            margin-bottom: 10px;
        }

        .form-title p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #2c2c2c;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d1d1;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #0078d4;
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 120, 212, 0.2);
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background-color: #0078d4;
            color: white;
        }

        .btn-primary:hover {
            background-color: #106ebe;
        }

        .btn-block {
            width: 100%;
        }

        .form-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }

        .form-footer a {
            color: #0078d4;
            text-decoration: none;
            font-weight: 500;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        .tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #eaeaea;
        }

        .tab {
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 500;
            color: #666;
            cursor: pointer;
            position: relative;
        }

        .tab.active {
            color: #0078d4;
        }

        .tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #0078d4;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-danger {
            background-color: #fde8e8;
            color: #e53e3e;
            border: 1px solid #feb2b2;
        }

        .alert-success {
            background-color: #e6fffa;
            color: #0694a2;
            border: 1px solid #b2f5ea;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="auth-container">
            <div class="auth-image">
                <div class="auth-image-content">
                    <h2>Welcome to Microsoft Copilot Clone</h2>
                    <p>Your AI-powered productivity assistant to help you stay organized and efficient. Get personalized suggestions and manage your tasks with ease.</p>
                    <svg width="120" height="120" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2 17L12 22L22 17" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2 12L12 17L22 12" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
            <div class="auth-form">
                <div class="logo">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 23 23" width="32" height="32">
                        <rect x="1" y="1" width="10" height="10" fill="#f25022"/>
                        <rect x="12" y="1" width="10" height="10" fill="#7fba00"/>
                        <rect x="1" y="12" width="10" height="10" fill="#00a4ef"/>
                        <rect x="12" y="12" width="10" height="10" fill="#ffb900"/>
                    </svg>
                    <h1>Microsoft Copilot Clone</h1>
                </div>

                <div class="tabs">
                    <div class="tab active" id="login-tab">Login</div>
                    <div class="tab" id="signup-tab">Sign Up</div>
                </div>

                <div id="error-message" class="alert alert-danger" style="display:none;"></div>
                <div id="success-message" class="alert alert-success" style="display:none;"></div>

                <div class="tab-content active" id="login-content">
                    <div class="form-title">
                        <h2>Sign in to your account</h2>
                        <p>Enter your credentials to access your dashboard</p>
                    </div>
                    <form id="login-form" method="post">
                        <div class="form-group">
                            <label for="login-email">Email address</label>
                            <input type="email" id="login-email" name="email" class="form-control" placeholder="Enter your email" required>
                        </div>
                        <div class="form-group">
                            <label for="login-password">Password</label>
                            <input type="password" id="login-password" name="password" class="form-control" placeholder="Enter your password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                    </form>
                    <div class="form-footer">
                        Don't have an account? <a href="#" id="show-signup">Sign up</a>
                    </div>
                </div>

                <div class="tab-content" id="signup-content">
                    <div class="form-title">
                        <h2>Create a new account</h2>
                        <p>Fill in your details to get started</p>
                    </div>
                    <form id="signup-form" method="post">
                        <div class="form-group">
                            <label for="signup-name">Full Name</label>
                            <input type="text" id="signup-name" name="name" class="form-control" placeholder="Enter your full name" required>
                        </div>
                        <div class="form-group">
                            <label for="signup-email">Email address</label>
                            <input type="email" id="signup-email" name="email" class="form-control" placeholder="Enter your email" required>
                        </div>
                        <div class="form-group">
                            <label for="signup-password">Password</label>
                            <input type="password" id="signup-password" name="password" class="form-control" placeholder="Create a password" required>
                        </div>
                        <div class="form-group">
                            <label for="signup-confirm-password">Confirm Password</label>
                            <input type="password" id="signup-confirm-password" name="confirm_password" class="form-control" placeholder="Confirm your password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Create Account</button>
                    </form>
                    <div class="form-footer">
                        Already have an account? <a href="#" id="show-login">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab switching functionality
            const loginTab = document.getElementById('login-tab');
            const signupTab = document.getElementById('signup-tab');
            const loginContent = document.getElementById('login-content');
            const signupContent = document.getElementById('signup-content');
            const showLogin = document.getElementById('show-login');
            const showSignup = document.getElementById('show-signup');
            const errorMessage = document.getElementById('error-message');
            const successMessage = document.getElementById('success-message');
            const loginForm = document.getElementById('login-form');
            const signupForm = document.getElementById('signup-form');

            function showErrorMessage(message) {
                errorMessage.textContent = message;
                errorMessage.style.display = 'block';
                successMessage.style.display = 'none';
                setTimeout(() => {
                    errorMessage.style.display = 'none';
                }, 5000);
            }

            function showSuccessMessage(message) {
                successMessage.textContent = message;
                successMessage.style.display = 'block';
                errorMessage.style.display = 'none';
                setTimeout(() => {
                    successMessage.style.display = 'none';
                }, 5000);
            }

            function switchToLogin() {
                loginTab.classList.add('active');
                signupTab.classList.remove('active');
                loginContent.classList.add('active');
                signupContent.classList.remove('active');
            }

            function switchToSignup() {
                signupTab.classList.add('active');
                loginTab.classList.remove('active');
                signupContent.classList.add('active');
                loginContent.classList.remove('active');
            }

            loginTab.addEventListener('click', switchToLogin);
            signupTab.addEventListener('click', switchToSignup);
            showLogin.addEventListener('click', switchToLogin);
            showSignup.addEventListener('click', switchToSignup);

            // Handle login form submission
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const email = document.getElementById('login-email').value;
                const password = document.getElementById('login-password').value;
                
                // Create form data for the AJAX request
                const formData = new FormData();
                formData.append('email', email);
                formData.append('password', password);
                formData.append('action', 'login');
                
                // Send the AJAX request
                fetch('auth.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccessMessage('Login successful! Redirecting to dashboard...');
                        setTimeout(() => {
                            window.location.href = 'dashboard.php';
                        }, 1000);
                    } else {
                        showErrorMessage(data.message || 'An error occurred during login.');
                    }
                })
                .catch(error => {
                    showErrorMessage('An error occurred. Please try again.');
                    console.error('Error:', error);
                });
            });

            // Handle signup form submission
            signupForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const name = document.getElementById('signup-name').value;
                const email = document.getElementById('signup-email').value;
                const password = document.getElementById('signup-password').value;
                const confirmPassword = document.getElementById('signup-confirm-password').value;
                
                // Check if passwords match
                if (password !== confirmPassword) {
                    showErrorMessage('Passwords do not match.');
                    return;
                }
                
                // Create form data for the AJAX request
                const formData = new FormData();
                formData.append('name', name);
                formData.append('email', email);
                formData.append('password', password);
                formData.append('action', 'register');
                
                // Send the AJAX request
                fetch('auth.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccessMessage('Registration successful! You can now log in.');
                        signupForm.reset();
                        switchToLogin();
                    } else {
                        showErrorMessage(data.message || 'An error occurred during registration.');
                    }
                })
                .catch(error => {
                    showErrorMessage('An error occurred. Please try again.');
                    console.error('Error:', error);
                });
            });
        });
    </script>
</body>
</html>
