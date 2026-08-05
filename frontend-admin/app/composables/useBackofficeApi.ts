export type BackofficeEnvelope<T> = { data: T; meta?: { request_id?: string; generated_at?: string } }

export function useBackofficeApi() {
  const config = useRuntimeConfig()

  async function read<T>(path: string): Promise<BackofficeEnvelope<T>> {
    return await $fetch<BackofficeEnvelope<T>>(`${config.public.apiBase}${path}`, {
      method: 'GET',
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'include',
      cache: 'no-store',
      retry: 0
    })
  }

  return { read }
}
