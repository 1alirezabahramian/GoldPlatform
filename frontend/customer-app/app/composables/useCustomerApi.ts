export type CustomerEnvelope<T> = {
  data: T
  meta?: {
    request_id?: string
    generated_at?: string
  }
}

export type CustomerApiState<T> =
  | { status: 'loading'; data: null; message: null }
  | { status: 'ready'; data: T; message: null }
  | { status: 'empty'; data: null; message: string }
  | { status: 'error'; data: null; message: string }
  | { status: 'unavailable'; data: null; message: string }

type ReadOptions = { query?: Record<string, string | number | boolean> }
type MutationOptions = { body?: Record<string, unknown>; idempotencyKey?: string }

export function useCustomerApi() {
  const config = useRuntimeConfig()

  async function read<T>(path: string, options: ReadOptions = {}): Promise<CustomerEnvelope<T>> {
    return await $fetch<CustomerEnvelope<T>>(`${config.public.apiBase}${path}`, {
      method: 'GET',
      ...(options.query ? { query: options.query } : {}),
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'include',
      cache: 'no-store',
      retry: 0
    })
  }

  async function mutate<T>(path: string, method: 'POST' | 'PUT' | 'PATCH' | 'DELETE', options: MutationOptions = {}): Promise<CustomerEnvelope<T>> {
    const headers = new Headers({ Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' })
    if (options.idempotencyKey) headers.set('Idempotency-Key', options.idempotencyKey)

    return await $fetch<CustomerEnvelope<T>>(`${config.public.apiBase}${path}`, {
      method,
      body: options.body,
      headers,
      credentials: 'include',
      cache: 'no-store',
      retry: 0
    })
  }

  return { read, mutate }
}
