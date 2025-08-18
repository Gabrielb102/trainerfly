import axios, {AxiosResponse} from 'axios'

const {baseAPIURL, nonce} = localized

export function useAPI() {
  const api = axios.create({
    baseURL: baseAPIURL,
    headers: {
      'Content-Type': 'application/json',
      'X-WP-Nonce': nonce,
    },
  })

  const get = async (endpoint: string, params?: Record<string, any>) => {
    try {
      const response: AxiosResponse<any> = await api.get(endpoint, {params})
      return response.data
    } catch (error) {
      console.error('Error fetching data:', error)
      throw error
    }
  }

  return {get}
}
