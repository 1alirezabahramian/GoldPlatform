export interface QueueItem {
  id: number
  status: string
  created_at: string | null
  type?: string | null
  asset_type?: string | null
  quantity?: string | null
  unit?: string | null
  expires_at?: string | null
  reference?: string | null
  branch_code?: string | null
  requested_for?: string | null
}

export interface QueuePage {
  data: QueueItem[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}
