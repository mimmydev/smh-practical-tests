import { useAuthStore } from '@/stores/auth'

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000'

export function useApi() {
  const getAuthHeaders = (): Record<string, string> => {
    const authStore = useAuthStore()
    const headers: Record<string, string> = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    }

    if (authStore.token) {
      headers['Authorization'] = `Bearer ${authStore.token}`
    }

    return headers
  }

  const handleResponse = async <T>(response: Response): Promise<T> => {
    if (response.status === 401) {
      // Token expired or invalid
      const authStore = useAuthStore()
      authStore.clearAuth()
      throw new Error('Unauthorized - please login again')
    }

    if (!response.ok) {
      const error = await response.text()
      throw new Error(error || `HTTP error! status: ${response.status}`)
    }

    const contentType = response.headers.get('content-type')
    if (contentType && contentType.includes('application/json')) {
      return response.json()
    } else {
      return response.text() as T
    }
  }

  const get = async <T>(endpoint: string, params?: Record<string, string>): Promise<T> => {
    const url = new URL(`${API_BASE_URL}/api${endpoint}`)
    if (params) {
      Object.entries(params).forEach(([key, value]) => {
        url.searchParams.append(key, value)
      })
    }

    const response = await fetch(url.toString(), {
      method: 'GET',
      headers: getAuthHeaders(),
    })

    return handleResponse<T>(response)
  }

  const post = async <T>(endpoint: string, data?: Record<string, unknown>): Promise<T> => {
    const response = await fetch(`${API_BASE_URL}/api${endpoint}`, {
      method: 'POST',
      headers: getAuthHeaders(),
      body: data ? JSON.stringify(data) : undefined,
    })

    return handleResponse<T>(response)
  }

  const put = async <T>(endpoint: string, data?: Record<string, unknown>): Promise<T> => {
    const response = await fetch(`${API_BASE_URL}/api${endpoint}`, {
      method: 'PUT',
      headers: getAuthHeaders(),
      body: data ? JSON.stringify(data) : undefined,
    })

    return handleResponse<T>(response)
  }

  const del = async <T>(endpoint: string): Promise<T> => {
    const response = await fetch(`${API_BASE_URL}/api${endpoint}`, {
      method: 'DELETE',
      headers: getAuthHeaders(),
    })

    return handleResponse<T>(response)
  }

  const patch = async <T>(endpoint: string, data?: Record<string, unknown>): Promise<T> => {
    const response = await fetch(`${API_BASE_URL}/api${endpoint}`, {
      method: 'PATCH',
      headers: getAuthHeaders(),
      body: data ? JSON.stringify(data) : undefined,
    })

    return handleResponse<T>(response)
  }

  return {
    get,
    post,
    put,
    delete: del, // 'delete' is a reserved word, so we use 'del' and expose it as 'delete'
    patch
  }
}
