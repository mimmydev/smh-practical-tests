import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useAuthApi } from '@/composables/useAuthApi'

export interface User {
  id: number
  email: string
  name: string
  role: string
}

export interface AuthState {
  token: string | null
  user: User | null
  isLoading: boolean
  error: string | null
}

export const useAuthStore = defineStore('auth', () => {
  // Initialize auth API
  const authApi = useAuthApi()

  // State
  const token = ref<string | null>(localStorage.getItem('auth_token'))
  const user = ref<User | null>(JSON.parse(localStorage.getItem('auth_user') || 'null'))
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  // Getters
  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'admin')
  const isExhibitor = computed(() => user.value?.role === 'exhibitor' || user.value?.role === 'attendee')
  const userName = computed(() => user.value?.name || '')

  // Actions
  const setToken = (newToken: string) => {
    token.value = newToken
    localStorage.setItem('auth_token', newToken)
  }

  const setUser = (newUser: User) => {
    user.value = newUser
    localStorage.setItem('auth_user', JSON.stringify(newUser))
  }

  const setLoading = (loading: boolean) => {
    isLoading.value = loading
  }

  const setError = (newError: string | null) => {
    error.value = newError
  }

  const clearAuth = () => {
    token.value = null
    user.value = null
    isLoading.value = false
    error.value = null
    localStorage.removeItem('auth_token')
    localStorage.removeItem('auth_user')
  }

  const login = async (email: string, password: string) => {
    setLoading(true)
    setError(null)

    try {
      const data = await authApi.login(email, password)

      if (data.token) {
        setToken(data.token)
        setUser(data.user)
        return { success: true }
      } else {
        throw new Error('No token received')
      }
    } catch (err) {
      const message = err instanceof Error ? err.message : 'Login failed'
      setError(message)
      return { success: false, error: message }
    } finally {
      setLoading(false)
    }
  }

  const register = async (name: string, email: string, password: string, password_confirmation: string) => {
    setLoading(true)
    setError(null)

    try {
      const data = await authApi.register(name, email, password, password_confirmation)

      // Note: The register endpoint might not return a token
      // User may need to login separately
      return { success: true, message: data.message }
    } catch (err) {
      const message = err instanceof Error ? err.message : 'Registration failed'
      setError(message)
      return { success: false, error: message }
    } finally {
      setLoading(false)
    }
  }

  const logout = () => {
    clearAuth()
  }

  // Initialize store on app start
  const initialize = () => {
    const savedToken = localStorage.getItem('auth_token')
    const savedUser = localStorage.getItem('auth_user')

    if (savedToken) {
      token.value = savedToken
    }

    if (savedUser) {
      try {
        user.value = JSON.parse(savedUser)
      } catch {
        localStorage.removeItem('auth_user')
      }
    }
  }

  return {
    // State
    token,
    user,
    isLoading,
    error,

    // Getters
    isAuthenticated,
    isAdmin,
    isExhibitor,
    userName,

    // Actions
    setToken,
    setUser,
    setLoading,
    setError,
    clearAuth,
    login,
    register,
    logout,
    initialize,
  }
})
