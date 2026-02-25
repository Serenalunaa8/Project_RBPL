<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | CV Cipta Manunggal Konsultan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styling/css/login.css">
</head>
<body>
tes lagilagi
<div class="hero-bg"></div>

<div class="container">

    <!-- LEFT SIDE -->
    <div class="left">

        <div class="logo">
    <svg viewBox="0 0 120 120" class="logo-arch">
        <!-- Outline Frame -->
        <rect x="10" y="10" width="100" height="100" stroke="#ffc107" stroke-width="3" fill="none"/>
        
        <!-- Architectural CM Monogram -->
        <path d="M35 80 V40 H60" stroke="#ffc107" stroke-width="4" fill="none"/>
        <path d="M60 40 L75 60 L90 40 V80" stroke="#ffc107" stroke-width="4" fill="none"/>
    </svg>
    
    <h1>CV CIPTA<span> MANUNGGAL KONSULTAN</span></h1>
</div>

        <h2>Professional Architectural<br>Project Supervision</h2>

        <p>
            Sistem pengawasan proyek terintegrasi untuk
            memastikan presisi desain, kontrol kualitas,
            dan transparansi dalam setiap tahapan pembangunan.
        </p>

        <div class="mid-accent">
    <div class="vertical-lines"></div>
    <div class="tagline">
        Excellence in Structural Vision
    </div>
</div>

        <div class="building">
          <div class="tower">
            <div class="windows"></div>
         </div>
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

<script>
const windowContainer = document.querySelector(".windows");
for(let i=0;i<60;i++){
    const w = document.createElement("div");
    windowContainer.appendChild(w);
}
</script>

</body>
</html>