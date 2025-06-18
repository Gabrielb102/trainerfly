import axios, {AxiosResponse} from 'axios'

const {baseURL, nonce} = localized

export function useAPI() {
  const api = axios.create({
    baseURL,
    headers: {
      'Content-Type': 'application/json',
      'X-WP-Nonce': nonce,
    },
  })

  const get = async (endpoint: string, params?: Record<string, any>) => {
    try {
      const response: AxiosResponse<any> = await api.get(endpoint, {params})
      console.log('Response: ', response.data)
      return response.data
    } catch (error) {
      console.error('Error fetching data:', error)
      throw error
    }
  }

  return {get}
}
