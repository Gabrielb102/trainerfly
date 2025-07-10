import { ref } from 'vue'

export const useLoading = () => {
  const loading = ref(true)

  const startLoading = () => {
    loading.value = true
  }

  const stopLoading = () => {
    loading.value = false
  }

  const loadWatcherCallback = (newVal: Array<any>, oldVal: Array<any>) => {
    if (newVal.length > 0) {
      stopLoading()
    } else if (newVal.length === 0 && oldVal.length > 0) {
      startLoading()
    } else if (newVal.length === 0 && oldVal.length === 0) {
      stopLoading()
    }
  }

  return {
    loading,
    startLoading,
    stopLoading,
    loadWatcherCallback
  }
}

