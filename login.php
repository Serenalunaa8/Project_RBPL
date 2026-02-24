<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | CV Cipta Manunggal Konsultan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styling/css/style.css">
</head>
<body>

<div class="hero-bg"></div>

<div class="container">

    <!-- LEFT SIDE -->
    <div class="left">

        <div class="logo">
            <div class="logo-box"></div>
            <h1>CIPTA<span>MANUNGGAL</span></h1>
        </div>

        <h2>Professional Architectural<br>Project Supervision</h2>

        <p>
            Sistem pengawasan proyek terintegrasi untuk
            memastikan presisi desain, kontrol kualitas,
            dan transparansi dalam setiap tahapan pembangunan.
        </p>

        <div class="building-image">
            <img src="assets/img/building.png" alt="Premium Building">
        </div>

    </div>


    <!-- RIGHT SIDE -->
    <div class="right">

        <div class="login-card">

            <h3>Corporate Login</h3>
            <p>Masuk ke sistem monitoring proyek</p>

            <form action="proses_login.php" method="POST">

                <div class="form-group">
                    <input type="text" name="username" required>
                    <label>Username</label>
                </div>

                <div class="form-group">
                    <input type="password" name="password" required>
                    <label>Password</label>
                </div>

                <button type="submit">Sign In</button>

            </form>

        </div>

    </div>

</div>

</body>
</html>