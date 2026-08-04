export type BackofficePanel = 'admin' | 'operator'

export interface BackofficeUser {
  id: number
  display_name: string
  mobile_masked: string | null
  is_active: boolean
  last_login_at: string | null
}

export interface NavigationItem {
  code: string
  path: string
  label: string
  permission: string
}

export interface BootstrapData {
  panel: BackofficePanel
  user: BackofficeUser
  roles: string[]
  permissions: string[]
  navigation: NavigationItem[]
  capabilities: string[]
}

export interface ApiEnvelope<T> {
  data: T
  meta: { request_id: string; generated_at: string; api_version: 'v1' }
  message: string | null
}
