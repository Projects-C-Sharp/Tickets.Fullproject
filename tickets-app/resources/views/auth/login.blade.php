@extends('layouts.guest')
@section('title','Iniciar sesion')
@section('content')
<div class="auth-wrap">
  <div class="auth-visual">
    <div class="auth-visual__bg" style="background-image:url('/images/theater-curtain.jpg')"></div>
    <div class="auth-visual__text">
      <div class="auth-visual__title">Bienvenido<br><em>de vuelta</em></div>
      <p class="auth-visual__quote">"El teatro es la vida misma puesta en escena."</p>
    </div>
  </div>
  <div class="auth-panel">
    <div class="auth-form-box">
      <a href="{{ route('home') }}" style="display:block;margin-bottom:2rem">
        <span style="font-family:var(--font-display);font-size:1.6rem;font-weight:900;color:var(--c-dark)">Kep<span style="color:var(--c-orange)">ler</span></span>
      </a>
      <h1 style="font-size:2rem;margin-bottom:.5rem">Iniciar sesion</h1>
      <p class="text-muted" style="margin-bottom:2rem">Accede a tus entradas y pedidos</p>
      @if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
      @if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif
      <a href="{{ route('auth.google') }}" class="btn-google">
        <svg width="20" height="20" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
        Continuar con Google
      </a>
      <div class="divider">o con tu correo</div>
      <form method="POST" action="{{ route('login.post') }}">@csrf
        @if(request('redirect'))<input type="hidden" name="redirect" value="{{ request('redirect') }}">@endif
        <div class="form-group">
          <label class="form-label">Correo electronico</label>
          <input type="email" name="email" required class="form-input" placeholder="tu@correo.com" value="{{ old('email') }}">
        </div>
        <div class="form-group">
          <label class="form-label">Contrasena</label>
          <input type="password" name="password" required class="form-input" placeholder="Minimo 6 caracteres">
        </div>
        <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top:.5rem">Iniciar sesion</button>
      </form>
      <p style="text-align:center;margin-top:1.5rem;color:var(--text-muted);font-size:.9rem">
        No tienes cuenta? <a href="{{ route('register') }}" style="color:var(--c-orange);font-weight:600">Registrate gratis</a>
      </p>
    </div>
  </div>
</div>
@endsection
