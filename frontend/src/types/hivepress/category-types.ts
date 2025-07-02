export type Category = {
  id: number
  name: string
  slug: string
  description: string
  parent: number
  icon?: string | null
  image?: string | null
  listing_count?: number,
  has_children?: boolean
  url?: {
    errors?: {
      invalid_term?: Array<string>
    }
    error_data?: Array<any>
  }
}