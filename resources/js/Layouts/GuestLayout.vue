<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 px-4 py-10 text-white">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -left-24 top-10 h-56 w-56 rounded-full bg-cyan-500/15 blur-3xl" />
            <div class="absolute right-0 top-32 h-48 w-72 rotate-12 bg-pink-500/10 blur-3xl" />
            <div class="absolute bottom-0 left-1/3 h-40 w-40 rounded-full bg-blue-500/10 blur-3xl" />
        </div>

        <div class="relative w-full max-w-md space-y-6">
            <Link href="/" class="flex items-center gap-3 text-lg font-semibold tracking-tight text-white/90 transition hover:text-white">
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-cyan-500 to-blue-500 text-sm font-bold text-white shadow-lg shadow-cyan-500/30">
                    FA
                </span>
                <div class="leading-tight">
                    <p class="text-xs uppercase text-white/60">Faris Family Album</p>
                    <p class="text-base text-white">Dokumentasikan Kenanganmu</p>
                </div>
            </Link>

            <div class="rounded-3xl border border-white/10 bg-white/10 px-6 py-7 shadow-2xl shadow-slate-900/40 backdrop-blur">
                <slot />
            </div>
        </div>
    </div>
</template>
