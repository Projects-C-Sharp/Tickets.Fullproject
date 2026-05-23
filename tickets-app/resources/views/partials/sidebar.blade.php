<aside class="sidebar">
  <div class="sidebar__user">
    @if(session('user_photo'))
      <img src="{{ session('user_photo') }}" alt="Avatar" class="sidebar__avatar">
    @else
      <div class="sidebar__avatar-placeholder">{{ strtoupper(substr(session('user_name','U'),0,1)) }}</div>
    @endif
    <div>
      <div class="sidebar__name">{{ session('user_name', 'Usuario') }}</div>
      <div class="sidebar__email">{{ Str::limit(session('user_email',''), 22) }}</div>
    </div>
  </div>
  <div class="sidebar__section-label">Mi cuenta</div>
  <a href="{{ route('orders.index') }}" class="sidebar__link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
    Mis Entradas
  </a>
  <a href="{{ route('favorites.index') }}" class="sidebar__link {{ request()->routeIs('favorites.*') ? 'active' : '' }}">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
    Favoritos
  </a>
  <a href="{{ route('profile') }}" class="sidebar__link {{ request()->routeIs('profile') ? 'active' : '' }}">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    Mi Perfil
  </a>
  <a href="{{ route('pqrs.index') }}" class="sidebar__link {{ request()->routeIs('pqrs.*') ? 'active' : '' }}">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
    Soporte (PQRS)
  </a>
  <div style="flex:1"></div>
  <form method="POST" action="{{ route('logout') }}">@csrf
    <button type="submit" class="sidebar__link" style="width:100%;border:none;background:none;cursor:pointer;color:#888;text-align:left">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
      Cerrar sesión
    </button>
  </form>
</aside>
