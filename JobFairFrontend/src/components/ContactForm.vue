<template>
  <section id="contact" class="bg-gray-50 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Contact Us</h2>
        <p class="text-lg text-gray-600">
          Have questions about the job fair? We'd love to hear from you!
        </p>
      </div>

      <div class="bg-white rounded-lg shadow-md p-8">
        <form @submit.prevent="submitForm" class="space-y-6">
          <!-- Name Field -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
              Full Name *
            </label>
            <input
              id="name"
              v-model="formData.name"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'border-red-500': errors.name }"
            />
            <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name[0] }}</p>
          </div>

          <!-- Email Field -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
              Email Address *
            </label>
            <input
              id="email"
              v-model="formData.email"
              type="email"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'border-red-500': errors.email }"
            />
            <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email[0] }}</p>
          </div>

          <!-- Phone Field (Optional) -->
          <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
              Phone Number
            </label>
            <input
              id="phone"
              v-model="formData.phone"
              type="tel"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'border-red-500': errors.phone }"
              placeholder="Optional"
            />
            <p v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ errors.phone[0] }}</p>
          </div>

          <!-- Category Field -->
          <div>
            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
              Category *
            </label>
            <select
              id="category"
              v-model="formData.category"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'border-red-500': errors.category }"
            >
              <option value="">Select a category</option>
              <option value="general">General Inquiry</option>
              <option value="exhibitor">Exhibitor Question</option>
              <option value="visitor">Visitor Information</option>
              <option value="technical">Technical Support</option>
            </select>
            <p v-if="errors.category" class="mt-1 text-sm text-red-600">{{ errors.category[0] }}</p>
          </div>

          <!-- Subject Field -->
          <div>
            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">
              Subject *
            </label>
            <input
              id="subject"
              v-model="formData.subject"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'border-red-500': errors.subject }"
              maxlength="200"
            />
            <p v-if="errors.subject" class="mt-1 text-sm text-red-600">{{ errors.subject[0] }}</p>
          </div>

          <!-- Message Field -->
          <div>
            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">
              Message *
            </label>
            <textarea
              id="message"
              v-model="formData.message"
              required
              rows="6"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-vertical"
              :class="{ 'border-red-500': errors.message }"
              minlength="10"
              maxlength="2000"
              placeholder="Please provide details about your inquiry..."
            ></textarea>
            <p v-if="errors.message" class="mt-1 text-sm text-red-600">{{ errors.message[0] }}</p>
            <p class="mt-1 text-sm text-gray-500">
              {{ formData.message.length }}/2000 characters (minimum 10 required)
            </p>
          </div>

          <!-- Submit Button -->
          <div class="flex justify-center">
            <button
              type="submit"
              :disabled="isSubmitting"
              class="px-8 py-3 bg-blue-600 text-white font-medium rounded-md shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
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
                Sending...
              </span>
              <span v-else>Send Message</span>
            </button>
          </div>

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
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useApi } from '@/composables/useApi'

// Form data reactive object
const formData = reactive({
  name: '',
  email: '',
  phone: '',
  category: '',
  subject: '',
  message: '',
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
    email: '',
    phone: '',
    category: '',
    subject: '',
    message: '',
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
    errors.value.name = ['Please provide your name.']
    return
  }

  if (!formData.email.trim()) {
    errors.value.email = ['Please provide your email address.']
    return
  }

  if (!formData.category) {
    errors.value.category = ['Please select a category.']
    return
  }

  if (!formData.subject.trim()) {
    errors.value.subject = ['Please provide a subject.']
    return
  }

  if (!formData.message.trim()) {
    errors.value.message = ['Please provide your message.']
    return
  }

  if (formData.message.trim().length < 10) {
    errors.value.message = ['Please provide more details in your message (at least 10 characters).']
    return
  }

  isSubmitting.value = true

  try {
    const response = (await post('/contact', {
      name: formData.name.trim(),
      email: formData.email.trim(),
      phone: formData.phone.trim() || null,
      category: formData.category,
      subject: formData.subject.trim(),
      message: formData.message.trim(),
    })) as { data: { message?: string; data?: { id: string; status: string } } }

    // Show success message
    successMessage.value =
      response.data.message || 'Thank you for your message. We will get back to you soon!'

    // Reset form after successful submission
    resetForm()

    // Scroll to success message
    setTimeout(() => {
      const contactSection = document.getElementById('contact')
      if (contactSection) {
        contactSection.scrollIntoView({ behavior: 'smooth' })
      }
    }, 100)
  } catch (error: unknown) {
    console.error('Contact form submission error:', error)

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
          'Sorry, there was an error submitting your message. Please try again.'
      }
    } else {
      // Handle unknown errors
      generalError.value = 'Sorry, there was an error submitting your message. Please try again.'
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>
