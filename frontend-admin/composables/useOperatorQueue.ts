import type { QueuePage } from '~/types/operator-queue'

export function useOperatorQueue(kind: 'orders' | 'deliveries') {
  const config = useRuntimeConfig()
  const page = ref(1)
  const status = ref('')
  const selected = ref<number | null>(null)
  const query = computed(() => ({ page: page.value, per_page: 25, ...(status.value ? { status: status.value } : {}) }))
  const { data, pending, error, refresh } = useFetch<{ data: QueuePage }>(
    () => `${config.public.apiBase}/api/v1/operator/${kind}/queue`,
    { credentials: 'include', query, watch: [query] },
  )

  return {
    page,
    status,
    selected,
    queue: computed(() => data.value?.data ?? null),
    pending,
    error,
    refresh,
  }
}
