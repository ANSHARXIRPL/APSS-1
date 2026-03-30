<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>APSS - Loading</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      width: 100%;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      overflow: hidden;
    }

    .splash-container {
      text-align: center;
      animation: fadeInSplash 0.8s ease-in-out forwards;
    }

    .logo-wrapper {
      position: relative;
      width: 200px;
      height: 200px;
      margin: 0 auto 30px;
      animation: slideInLogo 1s ease-out forwards;
      opacity: 0;
    }

    .logo-wrapper img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      animation: scaleAndSpin 3s ease-in-out forwards;
    }

    .app-name {
      font-size: 2.5rem;
      font-weight: 700;
      color: white;
      margin-bottom: 15px;
      animation: fadeInText 1.2s ease-out 0.5s forwards;
      opacity: 0;
      letter-spacing: 2px;
    }

    .app-subtitle {
      font-size: 1rem;
      color: rgba(255, 255, 255, 0.9);
      margin-bottom: 40px;
      animation: fadeInText 1.2s ease-out 0.8s forwards;
      opacity: 0;
      font-weight: 300;
    }

    .loader {
      display: flex;
      justify-content: center;
      gap: 8px;
      animation: fadeInText 1.2s ease-out 1.1s forwards;
      opacity: 0;
    }

    .dot {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background-color: white;
      animation: bounce 1.4s infinite;
    }

    .dot:nth-child(1) {
      animation-delay: 0s;
    }

    .dot:nth-child(2) {
      animation-delay: 0.2s;
    }

    .dot:nth-child(3) {
      animation-delay: 0.4s;
    }

    @keyframes slideInLogo {
      from {
        opacity: 0;
        transform: translateY(-50px) scale(0.8);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    @keyframes scaleAndSpin {
      0% {
        transform: scale(0.8) rotateY(0deg);
        opacity: 0;
      }
      30% {
        transform: scale(1.1) rotateY(360deg);
      }
      100% {
        transform: scale(1) rotateY(720deg);
        opacity: 1;
      }
    }

    @keyframes fadeInText {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeInSplash {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    @keyframes bounce {
      0%, 100% {
        transform: translateY(0);
      }
      50% {
        transform: translateY(-15px);
      }
    }

    .progress-bar-custom {
      width: 150px;
      height: 4px;
      background-color: rgba(255, 255, 255, 0.2);
      border-radius: 2px;
      margin: 0 auto;
      overflow: hidden;
      animation: fadeInText 1.2s ease-out 1.1s forwards;
      opacity: 0;
    }

    .progress-fill {
      height: 100%;
      background: linear-gradient(90deg, transparent, white, transparent);
      animation: fillProgress 2s ease-in-out infinite;
      border-radius: 2px;
    }

    @keyframes fillProgress {
      0% {
        width: 0%;
      }
      50% {
        width: 100%;
      }
      100% {
        width: 0%;
      }
    }
  </style>
</head>
<body>
  <div class="splash-container">
    <div class="logo-wrapper">
      <img src="/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/assets/logo-apss.png" alt="Logo APSS">
    </div>
    <h1 class="app-name">APSS</h1>
    <p class="app-subtitle">Aplikasi Pengaduan Sarana Sekolah</p>
    
    <div class="loader">
      <div class="dot"></div>
      <div class="dot"></div>
      <div class="dot"></div>
    </div>

    <div class="progress-bar-custom">
      <div class="progress-fill"></div>
    </div>
  </div>

  <script>
    // Durasi splash screen 3 detik, lalu redirect ke login
    setTimeout(() => {
      window.location.href = '/APSS( APLIKASI PENGADUAN SARANA SEKOLAH )/login.php';
    }, 3000);
  </script>
</body>
</html>
