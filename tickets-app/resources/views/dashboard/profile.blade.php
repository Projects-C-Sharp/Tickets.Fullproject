@extends('layouts.app')
@section('title','Mi Perfil')
@section('content')
<div class="dashboard-layout">
  @include('partials.sidebar')
  <div class="dashboard-main">
    <h1 style="font-size:2rem;margin-bottom:.25rem">Mi <span class="text-orange">Perfil</span></h1>
    <p class="text-muted" style="margin-bottom:2rem">Gestiona tu informacion personal</p>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div style="display:grid;grid-template-columns:1fr 2fr;gap:2rem;align-items:start">
      <div class="card" style="padding:2rem;text-align:center">
        @if(session('user_photo'))
          <img src="{{ session('user_photo') }}" alt="Foto" style="width:120px;height:120px;border-radius:50%;object-fit:cover;margin:0 auto 1rem;border:4px solid var(--c-orange)">
        @else
          <div style="width:120px;height:120px;border-radius:50%;background:var(--c-orange);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:3rem;color:#fff;font-weight:700">{{ strtoupper(substr(session('user_name','U'),0,1)) }}</div>
        @endif
        <div style="font-weight:700;font-size:1.1rem">{{ session('user_name') }}</div>
        <div class="text-muted" style="font-size:.85rem;margin-bottom:1.5rem">{{ session('user_email') }}</div>
        <form method="POST" action="{{ route('profile.photo') }}" enctype="multipart/form-data">@csrf
          <label style="display:block;cursor:pointer">
            <span class="btn btn-outline btn-sm btn-full">Cambiar foto</span>
            <input type="file" name="photo" accept="image/*" style="display:none" onchange="this.form.submit()">
          </label>
        </form>
      </div>
      <div class="card" style="padding:2rem">
        <h2 style="font-size:1.3rem;margin-bottom:1.5rem">Informacion personal</h2>
        <form method="POST" action="{{ route('profile.update') }}">@csrf
          <div class="form-group"><label class="form-label">Nombre completo</label><input type="text" name="fullName" class="form-input" value="{{ session('user_name') }}"></div>
          <div class="form-group"><label class="form-label">Correo electronico</label><input type="email" class="form-input" value="{{ session('user_email') }}" disabled style="opacity:.6;cursor:not-allowed"><span class="form-hint">El correo no se puede modificar</span></div>
          <div class="form-group"><label class="form-label">Telefono</label><input type="tel" name="phone" class="form-input" placeholder="+57 300 000 0000"></div>
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
