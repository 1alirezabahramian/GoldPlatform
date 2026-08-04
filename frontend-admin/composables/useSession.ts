export const useSession = () => {
  const user = useState<{ name: string; roles: string[] } | null>('admin-user', () => null)
  const permissions = useState<string[]>('admin-permissions', () => [])

  const setSession = (payload: { user: { name: string; roles: string[] }; permissions: string[] }) => {
    user.value = payload.user
    permissions.value = [...new Set(payload.permissions)]
  }

  const clearSession = () => {
    user.value = null
    permissions.value = []
  }

  return { user, permissions, setSession, clearSession }
}
