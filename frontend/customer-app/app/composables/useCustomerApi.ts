type CustomerEnvelope<T> = {
  data: T
  meta?: {
    request_id?: string
    generated_at?: string
  }
}

type CustomerApiOptions = {
  method?: 'GET' | 'POST'
  body?: Record<string, unknown>
  idempotencyKey?: string
}

export function useCustomerApi() {
  const config = useRuntimeConfig()

  async function request<T>(path: string, options: CustomerApiOptions = {}): Promise<CustomerEnvelope<T>> {
    const headers = new Headers({
      Accept: 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    })

    if (options.idempotencyKey) {
      headers.set('Idempotency-Key', options.idempotencyKey)
    }

    return await $fetch<CustomerEnvelope<T>>(`${config.public.apiBase}${path}`, {
      method: options.method ?? 'GET',
      body: options.body,
      headers,
      credentials: 'include',
      cache: 'no-store',
      retry: 0,
    })
  }

  return { request }
}
