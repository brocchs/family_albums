<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Loading from '@/Components/Loading.vue';
import { useLoading } from '@/composables/useLoading';

const page = usePage();
const { isLoading, loadingMessage } = useLoading();

const auth = computed(() => page.props.auth);
const flash = computed(() => page.props.flash || {});
</script>

<template>
    <div class="min-h-screen bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-slate-100">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -left-20 top-10 h-48 w-48 rounded-full bg-cyan-500/20 blur-3xl" />
            <div class="absolute right-0 top-20 h-40 w-72 rotate-12 bg-pink-500/10 blur-3xl" />
        </div>

        <header class="relative border-b border-white/10 bg-slate-950/70 backdrop-blur">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                <Link href="/" class="flex items-center gap-2 text-lg font-semibold tracking-tight">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-cyan-500 to-blue-500 text-sm font-bold text-white shadow-lg">FA</span>
                    <div class="leading-tight">
                        <p class="text-sm uppercase text-white/60">Faris Family Album</p>
                        <p class="text-base text-white">Dokumentasikan Kenanganmu</p>
                    </div>
                </Link>

                <div class="flex items-center gap-3 text-sm font-medium">
                    <Link
                        v-if="!auth?.user"
                        :href="route('login')"
                        class="rounded-full border border-white/20 px-4 py-2 text-white/90 transition hover:-translate-y-0.5 hover:border-white/40 hover:text-white hover:shadow-lg hover:shadow-cyan-500/10"
                    >
                        Masuk
                    </Link>

                    <div v-if="auth?.user" class="flex items-center gap-2">
                        <span class="hidden text-white/80 sm:inline">Halo, {{ auth.user.name }}</span>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="rounded-full bg-white/10 px-3 py-2 text-white transition hover:-translate-y-0.5 hover:bg-white/20"
                        >
                            Keluar
                        </Link>
                    </div>
                </div>
            </div>

            <div v-if="flash?.success" class="mx-auto max-w-6xl px-6 pb-4">
                <div class="rounded-lg border border-emerald-300/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100 shadow-lg shadow-emerald-500/10">
                    {{ flash.success }}
                </div>
            </div>
        </header>

        <main class="relative mx-auto max-w-6xl px-6 pb-16 pt-10">
            <slot />
        </main>
        
        <!-- Global Loading Component -->
        <Loading :show="isLoading" :message="loadingMessage" />
    </div>
</template>
