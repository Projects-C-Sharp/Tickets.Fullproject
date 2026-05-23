import Alpine from 'alpinejs'
import focus from '@alpinejs/focus'
Alpine.plugin(focus)
window.Alpine = Alpine
Alpine.start()
document.addEventListener('DOMContentLoaded', () => {
  const token = document.querySelector('meta[name="csrf-token"]')?.content
  if (token) window._csrfToken = token
})
