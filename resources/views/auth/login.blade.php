
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | GG Billiards & Autocare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
</head>
<body>
    <main id="authLoginPage">
        <div id="authLoginBackground">
            <img src="{{ asset('images/loginBackground.png') }}" alt="GG Billiards & Autocare Background">
        </div>
        <section id="authLoginContainer">
            <div id="authLoginBranding">
                <img src="{{ asset('images/logo.png') }}" alt="GG Billiards & Autocare Logo" id="authLoginLogo">
                <h2 id="authLoginBusinessName">GG BILLIARDS & AUTOCARE</h2>
                <p id="authLoginDepartment">Food &amp; Beverages</p>
                <h3 id="authLoginSystemName">POINT-OF-SALE SYSTEM</h3>
            </div>
            <div id="authLoginHeading">
                <h1>Welcome Back</h1>
                <p>Sign in to access your POS dashboard</p>
            </div>
            @if ($errors->any())<p role="alert">Invalid username or password.</p>@endif
            <form id="authLoginForm" method="POST" action="{{ route('login.store') }}">
                @csrf
                <div id="authLoginUsernameGroup">
                    <label for="authLoginUsername">Username</label>
                    <div id="authLoginUsernameField">
                        <span class="material-symbols-outlined authLoginInputIcon" aria-hidden="true">person</span>
                        <input type="text" id="authLoginUsername" name="username" placeholder="Username" autocomplete="username" required>
                    </div>
                </div>
                <div id="authLoginPasswordGroup">
                    <label for="authLoginPassword">Password</label>
                    <div id="authLoginPasswordField">
                        <span class="material-symbols-outlined authLoginInputIcon" aria-hidden="true">lock</span>
                        <input type="password" id="authLoginPassword" name="password" placeholder="Password" autocomplete="current-password" required>
                        <button type="button" id="authLoginPasswordToggle" aria-label="Show password">
                            <span class="material-symbols-outlined authLoginVisibilityIcon" aria-hidden="true">visibility_off</span>
                        </button>
                    </div>
                </div>
                <div id="authLoginOptions">
                    <label id="authLoginRememberLabel">
                        <input type="checkbox" id="authLoginRemember" name="remember">
                        <span>Remember me</span>
                    </label>
                    <span id="authLoginForgotPassword">Contact an administrator to reset your password.</span>
                </div>
                
                <button type="submit" id="authLoginSubmit">
                    <span>Log In</span>
                    <span class="authLoginArrowIcon" aria-hidden="true">→</span>
                </button>

            </form>
        </section>
    </main>
</body>
</html>
