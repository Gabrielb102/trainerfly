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
      console.log('loadWatcherCallback: Data loaded, stopping loading state. newVal:', newVal, 'oldVal:', oldVal)
      stopLoading()
    } else if (newVal.length === 0 && oldVal.length > 0) {
      console.log('loadWatcherCallback: Data cleared, starting loading state. newVal:', newVal, 'oldVal:', oldVal)
      startLoading()
    } else if (newVal.length === 0 && oldVal.length === 0) {
      console.log('loadWatcherCallback: No data present, stopping loading state. newVal:', newVal, 'oldVal:', oldVal)
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

