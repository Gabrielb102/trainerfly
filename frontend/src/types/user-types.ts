export interface User {
  // Core user properties
  ID: number
  user_login: string
  user_email: string
  display_name: string
  first_name?: string
  last_name?: string
  user_registered: string // Date string (YYYY-MM-DD HH:MM:SS)
  
  // User roles and capabilities
  roles: Array<string> // e.g., ['administrator']
  capabilities: Record<string, boolean> // Individual user capabilities e.g., {administrator: true}
  allcaps: Record<string, boolean> // All capabilities (role + individual)
  
  // HivePress specific data
  is_vendor: boolean
  pending_messages_count: number
}
