<template>
  <div class="min-h-screen bg-gray-100">
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
          <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
          <div class="flex items-center space-x-4">
            <span class="text-gray-700">Welcome, {{ authStore.userName }}!</span>
            <button
              @click="handleLogout"
              class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div
          class="border-4 border-dashed border-gray-200 rounded-lg h-96 flex items-center justify-center"
        >
          <div class="text-center">
            <h3 class="mt-2 text-sm font-medium text-gray-900">Dashboard Coming Soon</h3>
            <p class="mt-1 text-sm text-gray-500">This is your authenticated dashboard area.</p>
            <div class="mt-6">
              <div class="text-left max-w-md mx-auto">
                <p class="text-sm text-gray-700 mb-2"><strong>User Info:</strong></p>
                <p class="text-sm">ID: {{ authStore.user?.id }}</p>
                <p class="text-sm">Email: {{ authStore.user?.email }}</p>
                <p class="text-sm">Role: {{ authStore.user?.role }}</p>
                <p class="text-sm">Authenticated: {{ authStore.isAuthenticated }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const router = useRouter()

const handleLogout = () => {
  authStore.logout()
  router.push('/')
}
</script>
