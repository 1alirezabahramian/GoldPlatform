export type CustomerOrder = {
  type: string | null
  asset_type: string | null
  quantity: string | null
  unit: string | null
  status: string | null
  status_reason: string | null
  expires_at: string | null
  created_at: string | null
  updated_at: string | null
}

export type CustomerCustody = {
  reference: string
  asset_type: string | null
  title: string | null
  quantity: string | null
  weight: string | null
  fineness: string | null
  branch_code: string | null
  status: string | null
  acquired_at: string | null
  ready_at: string | null
  delivered_at: string | null
}

export type CustomerDelivery = {
  reference: string
  custody_reference: string | null
  branch_code: string | null
  requested_for: string | null
  status: string | null
  status_reason: string | null
  approved_at: string | null
  ready_at: string | null
  delivered_at: string | null
  rejected_at: string | null
  cancelled_at: string | null
  created_at: string | null
}

export type CustomerCollection<T> = {
  items: T[]
}
