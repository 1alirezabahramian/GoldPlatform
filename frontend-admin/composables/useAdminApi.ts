type ApiEnvelope<T> = {
  data: T
  meta: { request_id?: string; generated_at?: string; api_version: string }
  message: string | null
}

export const useAdminApi = () => {
  const config = useRuntimeConfig()

  const get = async <T>(path: string): Promise<ApiEnvelope<T>> => {
    return await $fetch<ApiEnvelope<T>>(`${config.public.apiBase}${path}`, {
      credentials: 'include',
      headers: { Accept: 'application/json' }
    })
  }

  return { get }
}
