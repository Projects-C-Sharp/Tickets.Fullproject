<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Teatro Kepler') — Kepler</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<nav class="navbar" x-data="{ open: false }">
  <a href="{{ route('home') }}" class="navbar__logo">Kep<span>ler</span></a>
  <div class="navbar__links" :class="{ 'open': open }">
    <a href="{{ route('home') }}" class="navbar__link {{ request()->routeIs('home') ? 'active' : '' }}">Cartelera</a>
    @auth
      <a href="{{ route('orders.index') }}" class="navbar__link {{ request()->routeIs('orders.*') ? 'active' : '' }}">Mis Entradas</a>
      <a href="{{ route('favorites.index') }}" class="navbar__link {{ request()->routeIs('favorites.*') ? 'active' : '' }}">Favoritos</a>
      <a href="{{ route('pqrs.index') }}" class="navbar__link {{ request()->routeIs('pqrs.*') ? 'active' : '' }}">Soporte</a>
      <a href="{{ route('profile') }}" class="navbar__link" style="display:flex;align-items:center;gap:.5rem">
        @if(session('user_photo'))
          <img src="{{ session('user_photo') }}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid var(--c-orange)">
        @else
          <div class="sidebar__avatar-placeholder" style="width:32px;height:32px;font-size:.85rem">{{ strtoupper(substr(session('user_name','U'),0,1)) }}</div>
        @endif
        {{ session('user_name', 'Mi cuenta') }}
      </a>
      <form method="POST" action="{{ route('logout') }}" style="display:inline">@csrf
        <button type="submit" class="btn btn-ghost btn-sm">Salir</button>
      </form>
    @else
      <a href="{{ route('login') }}" class="navbar__link">Iniciar sesión</a>
      <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Registrarse</a>
    @endauth
  </div>
  <button class="navbar__hamburger" @click="open = !open" aria-label="Menú">
    <span :style="open ? 'transform:rotate(45deg) translateY(7px)' : ''"></span>
    <span :style="open ? 'opacity:0' : ''"></span>
    <span :style="open ? 'transform:rotate(-45deg) translateY(-7px)' : ''"></span>
  </button>
</nav>

@if(session('success'))<div class="alert alert-success" style="margin:0;border-radius:0">✓ {{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-error" style="margin:0;border-radius:0">✗ {{ session('error') }}</div>@endif

<main class="page-enter">@yield('content')</main>

<footer class="footer">
  <div class="footer__grid">
    <div>
      <div class="footer__logo">Kep<span>ler</span></div>
      <p class="footer__desc">El teatro más importante de la ciudad. Cultura, arte y entretenimiento en un solo lugar.</p>
    </div>
    <div>
      <div class="footer__title">Explorar</div>
      <a href="{{ route('home') }}" class="footer__link">Cartelera</a>
      <a href="{{ route('register') }}" class="footer__link">Crear cuenta</a>
    </div>
    <div>
      <div class="footer__title">Ayuda</div>
      <a href="{{ route('pqrs.index') }}" class="footer__link">Soporte (PQRS)</a>
      <a href="mailto:contacto@kepler.andrescortes.dev" class="footer__link">contacto@kepler.andrescortes.dev</a>
    </div>
  </div>
  <div class="footer__bottom">&copy; {{ date('Y') }} Teatro Kepler. Todos los derechos reservados.</div>
</footer>
</body>
</html>
