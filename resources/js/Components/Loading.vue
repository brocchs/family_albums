<template>
  <div v-if="show" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center z-[9999]">
    <div class="bg-slate-900/90 border border-white/10 rounded-3xl p-12 shadow-2xl shadow-cyan-500/20 backdrop-blur">
      <div class="flex flex-col items-center space-y-6">
        <div class="w-20 h-20 border-4 border-gray-300 border-t-cyan-400 rounded-full animate-spin"></div>
        <div class="text-center">
          <p class="text-white font-semibold text-2xl">{{ message }}</p>
          <p class="text-white/60 text-lg mt-2">Mohon tunggu sebentar...</p>
        </div>
      </div>
    </div>
  </div>
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