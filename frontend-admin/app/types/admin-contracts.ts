export type AdminAuditLog = {
  id: number
  actor_id: number | null
  action: string
  subject_type: string | null
  subject_id: string | number | null
  request_id: string | null
  created_at: string | null
}

export type AdminOutboxMessage = {
  uuid: string
  event_type: string
  aggregate_type: string | null
  aggregate_id: string | number | null
  attempts: number
  available_at: string | null
  processed_at: string | null
  created_at: string | null
  updated_at: string | null
}
