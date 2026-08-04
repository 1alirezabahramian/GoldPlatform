import type { AdminOperationalDashboard, OperatorOperationalDashboard } from '~/types/dashboard'
import type { BackofficeEnvelope } from '~/types/backoffice'

export const useOperationalDashboard = () => {
  const config = useRuntimeConfig()

  const request = async <T>(path: string): Promise<T> => {
    const response = await $fetch<BackofficeEnvelope<T>>(path, {
      baseURL: config.public.apiBase,
      credentials: 'include',
      headers: { Accept: 'application/json' },
    })

    return response.data
  }

  return {
    admin: () => request<AdminOperationalDashboard>('/api/v1/admin/dashboard'),
    operator: () => request<OperatorOperationalDashboard>('/api/v1/operator/dashboard'),
  }
}
