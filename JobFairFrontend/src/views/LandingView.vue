<template>
  <div class="min-h-screen bg-white">
    <!-- Header Section -->
    <header class="bg-gradient-to-r from-blue-600 to-blue-800 text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center">
          <h1 class="text-4xl md:text-6xl font-bold mb-4">Tech Career Fair 2025</h1>
          <p class="text-xl md:text-2xl mb-8 text-blue-100">
            Connect with top companies and kickstart your tech career
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button
              @click="scrollToSection('countdown')"
              class="px-8 py-3 bg-white text-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition-colors"
            >
              Learn More
            </button>
            <ExhibitorRegistrationModal>
              <button
                class="px-8 py-3 bg-blue-700 text-white rounded-lg font-semibold hover:bg-blue-800 border-2 border-blue-400 transition-colors"
              >
                Be Our Exhibitor
              </button>
            </ExhibitorRegistrationModal>
          </div>
        </div>
      </div>
    </header>

    <!-- Live Countdown Timer Section -->
    <section id="countdown" class="py-16 bg-gray-50">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold text-gray-900 mb-8">Fair Starts In</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
          <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-3xl md:text-4xl font-bold text-blue-600">{{ countdown.days }}</div>
            <div class="text-gray-600">Days</div>
          </div>
          <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-3xl md:text-4xl font-bold text-blue-600">{{ countdown.hours }}</div>
            <div class="text-gray-600">Hours</div>
          </div>
          <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-3xl md:text-4xl font-bold text-blue-600">{{ countdown.minutes }}</div>
            <div class="text-gray-600">Minutes</div>
          </div>
          <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-3xl md:text-4xl font-bold text-blue-600">{{ countdown.seconds }}</div>
            <div class="text-gray-600">Seconds</div>
          </div>
        </div>
        <p class="text-lg text-gray-600">
          Date: <strong>February 15, 2025</strong> | Time: <strong>9:00 AM - 5:00 PM</strong>
        </p>
      </div>
    </section>

    <!-- Job Matching & Career Talk Sessions -->
    <section id="sessions" class="py-16">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Reserve Your Sessions</h2>
          <p class="text-lg text-gray-600">
            Book time slots for job matching sessions and career talks with industry experts
          </p>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
          <!-- Job Matching Sessions -->
          <div class="bg-white rounded-lg shadow-md p-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Job Matching Sessions</h3>
            <p class="text-gray-600 mb-6">
              One-on-one sessions with recruiters to match your skills with available positions
            </p>
            <div class="space-y-3 mb-6">
              <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                <span>9:00 AM - 9:30 AM</span>
                <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                  Reserve
                </button>
              </div>
              <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                <span>10:00 AM - 10:30 AM</span>
                <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                  Reserve
                </button>
              </div>
              <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                <span>2:00 PM - 2:30 PM</span>
                <button class="px-4 py-2 bg-gray-300 text-gray-500 rounded cursor-not-allowed">
                  Full
                </button>
              </div>
            </div>
            <button
              @click="openReservationModal('job-matching')"
              class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700"
            >
              View All Time Slots
            </button>
          </div>

          <!-- Career Talk Sessions -->
          <div class="bg-white rounded-lg shadow-md p-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Career Talk Sessions</h3>
            <p class="text-gray-600 mb-6">
              Inspiring talks from tech leaders about career growth and industry trends
            </p>
            <div class="space-y-3 mb-6">
              <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                <span>11:00 AM - "AI in Tech Careers"</span>
                <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                  Reserve
                </button>
              </div>
              <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                <span>1:00 PM - "Remote Work Future"</span>
                <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                  Reserve
                </button>
              </div>
              <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                <span>3:00 PM - "Startup vs Corporate"</span>
                <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                  Reserve
                </button>
              </div>
            </div>
            <button
              @click="openReservationModal('career-talks')"
              class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700"
            >
              View All Sessions
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Floor Plan & Directory -->
    <section id="floorplan" class="py-16 bg-gray-50">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Interactive Floor Plan</h2>
          <p class="text-lg text-gray-600">
            Click on booths to learn more about exhibitors and available positions
          </p>
        </div>

        <!-- Search and Filter -->
        <div class="flex flex-col md:flex-row gap-4 mb-8">
          <div class="flex-1">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search companies or job types..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          <select
            v-model="filterCategory"
            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="">All Categories</option>
            <option value="tech">Technology</option>
            <option value="finance">Finance</option>
            <option value="startup">Startups</option>
            <option value="enterprise">Enterprise</option>
          </select>
          <button
            @click="openFloorPlan"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            Open Floor Plan
          </button>
        </div>

        <!-- Floor Plan Preview -->
        <div class="bg-white rounded-lg shadow-md p-8">
          <div
            class="aspect-video bg-gray-100 rounded-lg flex items-center justify-center cursor-pointer hover:bg-gray-200 transition-colors"
            @click="openFloorPlan"
          >
            <div class="text-center">
              <svg
                class="w-16 h-16 mx-auto text-gray-400 mb-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                ></path>
              </svg>
              <p class="text-gray-600">Click to view interactive floor plan</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Form Section -->
    <ContactForm />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import ContactForm from '@/components/ContactForm.vue'
import ExhibitorRegistrationModal from '@/components/exhibitor/ExhibitorRegistrationModal.vue'

// Search and filter states
const searchQuery = ref('')
const filterCategory = ref('')

// Countdown state
const countdown = ref({
  days: 0,
  hours: 0,
  minutes: 0,
  seconds: 0,
})

// Countdown timer logic
const fairStartDate = new Date('2025-02-15T09:00:00') // February 15, 2025, 9:00 AM
let countdownInterval: number

const updateCountdown = () => {
  const now = new Date()
  const timeLeft = fairStartDate.getTime() - now.getTime()

  if (timeLeft > 0) {
    countdown.value.days = Math.floor(timeLeft / (1000 * 60 * 60 * 24))
    countdown.value.hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
    countdown.value.minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60))
    countdown.value.seconds = Math.floor((timeLeft % (1000 * 60)) / 1000)
  } else {
    countdown.value = { days: 0, hours: 0, minutes: 0, seconds: 0 }
  }
}

// Navigation functions
const scrollToSection = (sectionId: string) => {
  const section = document.getElementById(sectionId)
  if (section) {
    section.scrollIntoView({ behavior: 'smooth' })
  }
}

const openReservationModal = (type: string) => {
  console.log('Opening reservation modal for:', type)
  // TODO: Implement reservation modal
}

const openFloorPlan = () => {
  console.log('Opening floor plan with search:', searchQuery.value, 'filter:', filterCategory.value)
  // TODO: Implement floor plan modal/lightbox
}

// Lifecycle hooks
onMounted(() => {
  updateCountdown()
  countdownInterval = window.setInterval(updateCountdown, 1000)
})

onUnmounted(() => {
  if (countdownInterval) {
    clearInterval(countdownInterval)
  }
})
</script>
