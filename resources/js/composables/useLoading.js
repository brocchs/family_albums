import { ref } from 'vue'

const isLoading = ref(false)
const loadingMessage = ref('Loading...')

export function useLoading() {
  const startLoading = (message = 'Loading...') => {
    loadingMessage.value = message
    isLoading.value = true
  }

  const stopLoading = () => {
    isLoading.value = false
  }

  return {
    isLoading,
    loadingMessage,
    startLoading,
    stopLoading
  }
}