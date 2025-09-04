<template>
  <Dialog>
    <DialogTrigger as-child>
      <slot />
    </DialogTrigger>
    <DialogContent class="sm:max-w-[600px] max-h-[90vh] overflow-y-auto">
      <DialogHeader>
        <DialogTitle>Exhibitor Registration</DialogTitle>
        <DialogDescription>
          Join our job fair as an exhibitor! Fill out the form below and we'll review your
          application within 48 hours.
        </DialogDescription>
      </DialogHeader>

      <form @submit.prevent="submitForm" class="space-y-6">
        <!-- Company Name -->
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
            Company Name *
          </label>
          <input
            id="name"
            v-model="formData.name"
            type="text"
            required
            placeholder="Enter your company name"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            :class="{ 'border-red-500': errors.name }"
          />
          <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name[0] }}</p>
        </div>

        <!-- Contact Email -->
        <div>
          <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-1">
            Contact Email *
          </label>
          <input
            id="contact_email"
            v-model="formData.contact_email"
            type="email"
            required
            placeholder="contact@company.com"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            :class="{ 'border-red-500': errors.contact_email }"
          />
          <p v-if="errors.contact_email" class="mt-1 text-sm text-red-600">
            {{ errors.contact_email[0] }}
          </p>
        </div>

        <!-- Phone -->
        <div>
          <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
            Phone Number *
          </label>
          <input
            id="phone"
            v-model="formData.phone"
            type="tel"
            required
            placeholder="+60 12-345-6789"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            :class="{ 'border-red-500': errors.phone }"
          />
          <p v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ errors.phone[0] }}</p>
        </div>

        <!-- Website -->
        <div>
          <label for="website" class="block text-sm font-medium text-gray-700 mb-1">
            Website (Optional)
          </label>
          <input
            id="website"
            v-model="formData.website"
            type="url"
            placeholder="https://www.company.com"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            :class="{ 'border-red-500': errors.website }"
          />
          <p v-if="errors.website" class="mt-1 text-sm text-red-600">{{ errors.website[0] }}</p>
        </div>

        <!-- Industry -->
        <div>
          <label for="industry" class="block text-sm font-medium text-gray-700 mb-1">
            Industry *
          </label>
          <select
            id="industry"
            v-model="formData.industry"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            :class="{ 'border-red-500': errors.industry }"
          >
            <option value="">Select your industry</option>
            <option
              v-for="industry in INDUSTRY_OPTIONS"
              :key="industry.value"
              :value="industry.value"
            >
              {{ industry.label }}
            </option>
          </select>
          <p v-if="errors.industry" class="mt-1 text-sm text-red-600">{{ errors.industry[0] }}</p>
        </div>

        <!-- Address -->
        <div>
          <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
            Company Address *
          </label>
          <textarea
            id="address"
            v-model="formData.address"
            required
            rows="3"
            placeholder="Enter your company address"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-vertical"
            :class="{ 'border-red-500': errors.address }"
          ></textarea>
          <p v-if="errors.address" class="mt-1 text-sm text-red-600">{{ errors.address[0] }}</p>
        </div>

        <!-- Description -->
        <div>
          <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
            Company Description *
          </label>
          <textarea
            id="description"
            v-model="formData.description"
            required
            rows="5"
            placeholder="Tell us about your company, products, and services (minimum 50 characters)"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-vertical"
            :class="{ 'border-red-500': errors.description }"
            minlength="50"
            maxlength="2000"
          ></textarea>
          <p v-if="errors.description" class="mt-1 text-sm text-red-600">
            {{ errors.description[0] }}
          </p>
          <p class="mt-1 text-sm text-gray-500">
            {{ formData.description.length }}/2000 characters (minimum 50 required)
          </p>
        </div>

        <!-- Booth Preferences -->
        <div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Booth Preferences (Optional)
            </label>
            <p class="text-sm text-gray-500">
              Select your preferred booth types. This helps us with planning.
            </p>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div
              v-for="option in BOOTH_PREFERENCE_OPTIONS"
              :key="option.value"
              class="flex items-center space-x-3"
            >
              <input
                :id="`booth_${option.value}`"
                type="checkbox"
                :value="option.value"
                v-model="formData.booth_preferences"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <label :for="`booth_${option.value}`" class="text-sm text-gray-700">
                {{ option.label }}
              </label>
            </div>
          </div>
        </div>

        <DialogFooter class="flex flex-col sm:flex-row gap-3">
          <DialogClose as-child>
            <Button type="button" variant="outline" :disabled="isSubmitting"> Cancel </Button>
          </DialogClose>
          <button
            type="submit"
            :disabled="isSubmitting"
            class="px-6 py-2 bg-blue-600 text-white font-medium rounded-md shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="isSubmitting" class="flex items-center">
              <svg
                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"
                ></circle>
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
              </svg>
              Submitting...
            </span>
            <span v-else>Submit Application</span>
          </button>
        </DialogFooter>

        <!-- Success Message -->
        <div v-if="successMessage" class="p-4 bg-green-50 border border-green-200 rounded-md">
          <div class="flex">
            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd"
              />
            </svg>
            <p class="ml-2 text-sm text-green-700">{{ successMessage }}</p>
          </div>
        </div>

        <!-- Error Message -->
        <div v-if="generalError" class="p-4 bg-red-50 border border-red-200 rounded-md">
          <div class="flex">
            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
              <path
                fill-rule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                clip-rule="evenodd"
              />
            </svg>
            <p class="ml-2 text-sm text-red-700">{{ generalError }}</p>
          </div>
        </div>
      </form>
    </DialogContent>
  </Dialog>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
  DialogClose,
} from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { useApi } from '@/composables/useApi'
import { INDUSTRY_OPTIONS, BOOTH_PREFERENCE_OPTIONS } from './schema'

// Form data reactive object
const formData = reactive({
  name: '',
  contact_email: '',
  phone: '',
  website: '',
  industry: '',
  address: '',
  description: '',
  booth_preferences: [] as string[],
})

// State management
const isSubmitting = ref(false)
const successMessage = ref('')
const generalError = ref('')
const errors = ref<Record<string, string[]>>({})

const { post } = useApi()

// Reset form function
const resetForm = () => {
  Object.assign(formData, {
    name: '',
    contact_email: '',
    phone: '',
    website: '',
    industry: '',
    address: '',
    description: '',
    booth_preferences: [],
  })
  errors.value = {}
  successMessage.value = ''
  generalError.value = ''
}

// Submit form function
const submitForm = async () => {
  if (isSubmitting.value) return

  // Reset previous messages
  successMessage.value = ''
  generalError.value = ''
  errors.value = {}

  // Basic client-side validation
  if (!formData.name.trim()) {
    errors.value.name = ['Please provide your company name.']
    return
  }

  if (!formData.contact_email.trim()) {
    errors.value.contact_email = ['Please provide your contact email.']
    return
  }

  if (!formData.phone.trim()) {
    errors.value.phone = ['Please provide your phone number.']
    return
  }

  if (!formData.industry) {
    errors.value.industry = ['Please select your industry.']
    return
  }

  if (!formData.address.trim()) {
    errors.value.address = ['Please provide your company address.']
    return
  }

  if (!formData.description.trim()) {
    errors.value.description = ['Please provide your company description.']
    return
  }

  if (formData.description.trim().length < 50) {
    errors.value.description = [
      'Please provide a more detailed description (at least 50 characters).',
    ]
    return
  }

  isSubmitting.value = true

  try {
    const response = (await post('/exhibitors', {
      name: formData.name.trim(),
      contact_email: formData.contact_email.trim(),
      phone: formData.phone.trim(),
      website: formData.website.trim() || undefined,
      industry: formData.industry,
      address: formData.address.trim(),
      description: formData.description.trim(),
      booth_preferences: formData.booth_preferences,
    })) as { data: { message?: string; data?: { id: string; status: string } } }

    // Show success message
    successMessage.value =
      response.data.message ||
      'Registration submitted successfully! We will review your application and contact you within 48 hours.'

    // Reset form after successful submission
    resetForm()
  } catch (error: unknown) {
    console.error('Exhibitor registration error:', error)

    // Type guard for axios error
    if (error && typeof error === 'object' && 'response' in error) {
      const axiosError = error as {
        response?: {
          status?: number
          data?: { errors?: Record<string, string[]>; message?: string }
        }
      }

      if (axiosError.response?.status === 422 && axiosError.response.data?.errors) {
        // Handle validation errors from server
        errors.value = axiosError.response.data.errors
      } else {
        // Handle general errors
        generalError.value =
          axiosError.response?.data?.message ||
          'Sorry, there was an error submitting your registration. Please try again.'
      }
    } else {
      // Handle unknown errors
      generalError.value =
        'Sorry, there was an error submitting your registration. Please try again.'
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>
