import type { ApiEnvelope, BackofficePanel, BootstrapData } from '~/types/backoffice'

export const useBackofficeSession = () => {
  const session = useState<BootstrapData | null>('backoffice-session', () => null)
  const pending = useState<boolean>('backoffice-session-pending', () => false)
  const error = useState<'unauthenticated' | 'forbidden' | 'unavailable' | null>('backoffice-session-error', () => null)
  const config = useRuntimeConfig()

  const load = async (panel: BackofficePanel): Promise<BootstrapData | null> => {
    pending.value = true
    error.value = null

    try {
      const response = await $fetch<ApiEnvelope<BootstrapData>>(`${config.public.apiBase}/${panel}/bootstrap`, {
        credentials: 'include',
        headers: { Accept: 'application/json' },
      })

      session.value = response.data
      return response.data
    } catch (exception: any) {
      session.value = null
      const status = exception?.statusCode ?? exception?.response?.status
      error.value = status === 401 ? 'unauthenticated' : status === 403 ? 'forbidden' : 'unavailable'
      return null
    } finally {
      pending.value = false
    }
  }

  const can = (permission: string): boolean => session.value?.permissions.includes(permission) ?? false
  const clear = () => { session.value = null; error.value = null }

  return { session, pending, error, load, can, clear }
}
