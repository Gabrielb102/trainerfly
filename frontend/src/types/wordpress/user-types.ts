export interface WP_User {
  // Core user properties (top level)
  ID: number
  cap_key: string // Usually "wp_capabilities"
  filter: null
  roles: Array<string> // e.g., ['administrator']

  // User capabilities and roles
  caps: Record<string, boolean> // Individual user capabilities e.g., {administrator: true}
  allcaps: Record<string, boolean> // All capabilities (role + individual)

  // User data object (from wp_users table)
  data: {
    ID: string // Note: comes as string from backend
    user_login: string
    user_pass: string
    user_nicename: string
    user_email: string
    user_url: string
    user_registered: string // Date string (YYYY-MM-DD HH:MM:SS)
    user_activation_key: string
    user_status: string // Note: comes as string from backend
    display_name: string
  }

  // Common user meta fields (accessed via magic methods)
  first_name?: string
  last_name?: string
  description?: string
  nickname?: string
  wp_capabilities?: Record<string, boolean>
  wp_user_level?: string
  rich_editing?: string
  syntax_highlighting?: string
  comment_shortcuts?: string
  admin_color?: string
  use_ssl?: string
  show_admin_bar_front?: string
  locale?: string
}
