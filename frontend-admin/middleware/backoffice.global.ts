import type { BackofficePanel } from '~/types/backoffice'

export default defineNuxtRouteMiddleware(async (to) => {
  const panel = to.path.startsWith('/admin') ? 'admin' : to.path.startsWith('/operator') ? 'operator' : null
  if (!panel || to.path === '/unauthorized' || to.path === '/session-expired') return

  const { session, error, load } = useBackofficeSession()
  if (!session.value || session.value.panel !== panel) await load(panel as BackofficePanel)

  if (error.value === 'unauthenticated') return navigateTo('/session-expired')
  if (error.value === 'forbidden') return navigateTo('/unauthorized')
  if (!session.value) return navigateTo('/service-unavailable')

  if (session.value.panel !== panel) {
    return navigateTo(session.value.panel === 'admin' ? '/admin' : '/operator')
  }
})
