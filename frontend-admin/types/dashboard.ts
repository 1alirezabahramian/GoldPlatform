export interface OperationalQueueItem {
  id: number
  status: string
  created_at: string | null
}

export interface AdminOperationalDashboard {
  summary: {
    open_orders: number | null
    active_deliveries: number | null
    failed_settlements: number | null
    custody_items: number | null
    pending_outbox: number | null
  }
  queues: {
    orders: OperationalQueueItem[]
    deliveries: OperationalQueueItem[]
  }
  financial_metrics_supported: false
}

export interface OperatorOperationalDashboard {
  summary: {
    pending_orders: number | null
    approved_orders: number | null
    requested_deliveries: number | null
    ready_deliveries: number | null
  }
  queues: {
    orders: OperationalQueueItem[]
    deliveries: OperationalQueueItem[]
  }
}
