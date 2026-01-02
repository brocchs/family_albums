<template>
  <div class="relative overflow-hidden" :class="containerClass">
    <!-- Placeholder with fixed dimensions -->
    <div 
      v-if="!loaded && !error"
      class="w-full h-64 bg-slate-800 animate-pulse flex items-center justify-center"
      :class="skeletonClass"
    >
      <div class="w-8 h-8 border-2 border-white/20 border-t-cyan-400 rounded-full animate-spin"></div>
    </div>
    
    <!-- Actual image -->
    <img
      v-show="loaded && !error"
      :src="src"
      :alt="alt"
      :class="imageClass"
      :loading="lazy ? 'lazy' : 'eager'"
      @load="handleLoad"
      @error="handleError"
    />
    
    <!-- Error state -->
    <div 
      v-if="error"
      class="w-full h-64 bg-slate-800 flex items-center justify-center"
      :class="errorClass"
    >
      <div class="flex flex-col items-center gap-2 text-white/60">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01" />
        </svg>
        <span class="text-xs" v-if="errorText">{{ errorText }}</span>
      </div>
    </div>
    
    <!-- Overlay content -->
    <div v-if="loaded && !error" class="absolute inset-0">
      <slot />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
  src: {
    type: String,
    required: true
  },
  alt: {
    type: String,
    default: ''
  },
  lazy: {
    type: Boolean,
    default: true
  },
  showSkeleton: {
    type: Boolean,
    default: true
  },
  loadingText: {
    type: String,
    default: 'Memuat...'
  },
  errorText: {
    type: String,
    default: 'Gagal memuat gambar'
  },
  containerClass: {
    type: String,
    default: ''
  },
  imageClass: {
    type: String,
    default: 'w-full h-full object-cover'
  },
  skeletonClass: {
    type: String,
    default: ''
  },
  errorClass: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['load', 'error'])

const loaded = ref(false)
const error = ref(false)

const handleLoad = () => {
  loaded.value = true
  error.value = false
  emit('load')
}

const handleError = () => {
  loaded.value = false
  error.value = true
  emit('error')
}

onMounted(() => {
  // Preload image if not lazy
  if (!props.lazy) {
    const img = new Image()
    img.onload = handleLoad
    img.onerror = handleError
    img.src = props.src
  }
})
</script>

<style scoped>
/* Removed complex transitions for better performance */
</style>