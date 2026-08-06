export type OperatorOrder = {
  id: number
  user_id: number
  type: string | null
  asset_type: string | null
  asset_quantity: string | null
  asset_unit: string | null
  status: string
  gold_weight: string | null
  gold_price: string | null
  commission: string | null
  total_price: string | null
  expires_at: string | null
  created_at: string | null
}

export type OperatorDelivery = {
  id: number
  uuid: string
  custody_asset_id: number
  user_id: number
  branch_code: string | null
  requested_for: string | null
  status: string
  approved_at: string | null
  ready_at: string | null
  created_at: string | null
}
