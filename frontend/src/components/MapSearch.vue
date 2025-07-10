<script setup>
import ModeSwitch from '@/components/ModeSwitch.vue'
import { useMapSearch } from '@/composables/useMapSearch.ts'
import { useRouter } from 'vue-router'

const { query, suggestions, retrieve, preventSuggestions, selectedSuggestion } = useMapSearch()

// Function to handle the selection of a suggestion
const selectSuggestion = async (suggestion) => {
  // First, prevent any suggestions from appearing
  preventSuggestions()

  // Then update the selected suggestion and query
  selectedSuggestion.value = suggestion
  query.value = suggestion.name

  // Automatically retrieve the selected suggestion, better UX IMO
  retrieve(selectedSuggestion.value)
}

// Find and navigate to the selected place
const search = () => {
  retrieve(selectedSuggestion.value)
}

// Function to handle the 'enter' keyboard shortcut
const onEnter = async () => {
  if (suggestions.value && suggestions.value.length > 0) {
    await selectSuggestion(suggestions.value[0])
    await retrieve(selectedSuggestion.value)
  }
}

//#region Navigate to Remote Listing Screen

const router = useRouter()

const goToRemote = () => {
  router.push('/remote')
}

//#endregion

</script>

<template>
  <!--Goes within the Header component slot -->
  <div class="flex flex-col gap-2 justify-end h-full items-center">
    <div class="flex gap-2 justify-center items-center relative">
      <label for="location-search-field" class="uppercase font-extrabold">Location: </label>
      <div class="relative w-64">
        <UInput v-model="query" id="location-search-field" placeholder="Search across the nation"
                variant="soft" color="neutral" class="w-full" @keyup.enter="onEnter" />

        <!-- Suggestions Dropdown -->
        <div v-if="suggestions && suggestions.length > 0"
             class="absolute z-500 mt-1 w-full bg-white rounded-md shadow-lg max-h-60 overflow-auto">
          <ul class="p-0 m-0 rounded">
            <li
              v-for="suggestion in suggestions"
              :key="suggestion.mapbox_id"
              class="cursor-pointer hover:bg-gray-100 rounded-none, py-2 px-3"
              @click="selectSuggestion(suggestion)"
            >
              <div class="font-medium">{{ suggestion.name }}</div>
              <div class="text-sm text-gray-500">
                {{ suggestion.full_address || suggestion.place_formatted }}
              </div>
            </li>
          </ul>
        </div>
      </div>
      <UButton label="Search" @click="search" />
      <UButton label="Search for Remote" variant="outline"
               color="primary" @click="goToRemote" />
    </div>
    <div class="flex gap-2 justify-center items-center">
      <span class="uppercase font-bold">Let's:</span>
      <ModeSwitch labelOne="Learn" labelTwo="Share" />
    </div>
  </div>
</template>
