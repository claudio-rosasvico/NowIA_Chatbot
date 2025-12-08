<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>NowIA — Chatbots Inteligentes</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Styles -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="antialiased d-flex align-items-center min-vh-100 bg-brand-primary text-white">

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6 col-md-8 text-center">

        <!-- Brand Title -->
        <h1 class="display-3 fw-bold mb-3">
          <span class="text-brand-secondary">Now</span>IA
        </h1>

        <!-- Slogan -->
        <p class="lead mb-5 px-md-5 text-light opacity-75">
          EL futuro empieza ahora: Chatbots inteligentes, respuestas automáticas y conocimiento centralizado en una UI
          limpia.
        </p>

        <!-- Action Buttons -->
        <div class="d-grid gap-3 d-sm-flex justify-content-sm-center mb-5">
          <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-5 py-3 fw-bold">
            Ingresar
          </a>
          <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-5 py-3 fw-bold">
            Crear cuenta
          </a>
        </div>

        <!-- Features List -->
        <div
          class="text-start d-inline-block bg-dark bg-opacity-25 p-4 rounded-3 border border-secondary border-opacity-25">
          <h5 class="fw-bold mb-3 text-brand-secondary">Ventajas</h5>
          <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
            <li class="d-flex align-items-center">
              <i class="bi bi-lightning-charge-fill text-warning me-2"></i>
              <span>Rendimiento y dinamismo</span>
            </li>
            <li class="d-flex align-items-center">
              <i class="bi bi-robot text-info me-2"></i>
              <span>Entrenamiento con tus propios PDFs</span>
            </li>
            <li class="d-flex align-items-center">
              <i class="bi bi-chat-text-fill text-success me-2"></i>
              <span>Respuestas precisas vía WhatsApp/Web</span>
            </li>
            <li class="d-flex align-items-center">
              <i class="bi bi-shield-check text-primary me-2"></i>
              <span>Control total de tus fuentes de datos</span>
            </li>
          </ul>
        </div>

        <!-- Footer -->
        <div class="mt-5 text-muted small">
          &copy; {{ date('Y') }} NowIA. Todos los derechos reservados.
        </div>
      </div>
    </div>
  </div>
  <script defer src="http://localhost:8080/widget.js?key=wDs4KnmsB36dkVdW0XIyGMn11oAygQ9n1VHIXlB2"></script>

</body>

</html>