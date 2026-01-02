<template>
  <Transition name="loading-fade">
    <div v-if="show" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center z-[9999]">
      <div class="bg-slate-900/90 border border-white/10 rounded-3xl p-12 shadow-2xl shadow-cyan-500/20 backdrop-blur transform transition-all duration-300">
        <div class="flex flex-col items-center space-y-6">
          <div class="relative">
            <div class="w-20 h-20 border-4 border-gray-300/30 rounded-full"></div>
            <div class="absolute inset-0 w-20 h-20 border-4 border-transparent border-t-cyan-400 rounded-full animate-spin"></div>
            <div class="absolute inset-2 w-16 h-16 border-2 border-transparent border-t-blue-400 rounded-full animate-spin animation-delay-150"></div>
          </div>
          <div class="text-center">
            <p class="text-white font-semibold text-2xl animate-pulse">{{ message }}</p>
            <p class="text-white/60 text-lg mt-2">Mohon tunggu sebentar...</p>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
defineProps({
  show: {
    type: Boolean,
    default: false
  },
  message: {
    type: String,
    default: 'Loading...'
  }
})
</script>

<style scoped>
.loading-fade-enter-active,
.loading-fade-leave-active {
  transition: opacity 0.3s ease-in-out;
}

.loading-fade-enter-from,
.loading-fade-leave-to {
  opacity: 0;
}

.animation-delay-150 {
  animation-delay: 0.15s;
}
</style>