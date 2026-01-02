<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AlbumLayout from '@/Layouts/AlbumLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    albums: {
        type: Array,
        required: true,
    },
    canManage: {
        type: Boolean,
        default: false,
    },
});

const albumForm = useForm({
    title: '',
    description: '',
});

const accentClass = computed(() => 'from-cyan-400 via-blue-500 to-indigo-500');

const createAlbum = () => {
    albumForm.post(route('albums.store'), {
        preserveScroll: true,
        onSuccess: () => albumForm.reset(),
    });
};
</script>

<template>
    <Head title="Album Keluarga" />
    <AlbumLayout>
        <section class="grid gap-8 lg:grid-cols-2">
            <div class="space-y-5">
                <p class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white/80">
                    Album keluarga
                    <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-400" />
                </p>
                <h1 class="text-4xl font-bold leading-tight text-white sm:text-5xl">
                    Simpan momen cantik,
                    <span class="bg-gradient-to-r from-cyan-400 via-blue-500 to-purple-500 bg-clip-text text-transparent">
                        berbagi dengan hangat.
                    </span>
                </h1>
                <p class="max-w-2xl text-lg text-white/70">
                    Lihat galeri keluarga dengan grid yang rapi dan unggah foto baru tanpa ribet. Semua foto aman di satu tempat
                    dengan akses login untuk penyimpanan.
                </p>
                <div class="flex flex-wrap gap-3 text-sm text-white/80">
                    <span class="flex items-center gap-2 rounded-full bg-white/5 px-4 py-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-400" /> Upload setelah login
                    </span>
                    <span class="flex items-center gap-2 rounded-full bg-white/5 px-4 py-2">
                        <span class="h-2 w-2 rounded-full bg-cyan-400" /> Tampilan grid cantik
                    </span>
                    <span class="flex items-center gap-2 rounded-full bg-white/5 px-4 py-2">
                        <span class="h-2 w-2 rounded-full bg-blue-400" /> Support banyak album
                    </span>
                </div>
            </div>

            <div v-if="canManage" class="rounded-2xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-cyan-500/10 backdrop-blur">
                <h3 class="mb-4 text-lg font-semibold text-white">Buat album baru</h3>
                <form @submit.prevent="createAlbum" class="space-y-4">
                    <div>
                        <InputLabel for="title" value="Judul Album" />
                        <TextInput
                            id="title"
                            v-model="albumForm.title"
                            type="text"
                            class="mt-2 block w-full"
                            placeholder="Liburan Keluarga, Lebaran 2024, dll."
                            required
                        />
                        <InputError :message="albumForm.errors.title" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="description" value="Deskripsi singkat" />
                        <textarea
                            id="description"
                            v-model="albumForm.description"
                            rows="3"
                            class="mt-2 w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm text-white shadow-inner focus:border-cyan-400 focus:ring-0"
                            placeholder="Cerita di balik album ini..."
                        />
                        <InputError :message="albumForm.errors.description" class="mt-2" />
                    </div>
                    <PrimaryButton :disabled="albumForm.processing">
                        Simpan Album
                    </PrimaryButton>
                </form>
            </div>
        </section>

        <section class="mt-10">
            <div class="mb-4 flex items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-white">Album keluarga</h2>
                <p class="text-sm text-white/60">{{ albums.length }} album tersimpan</p>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="album in albums"
                    :key="album.id"
                    class="group overflow-hidden rounded-2xl border border-white/10 bg-white/5 shadow-xl shadow-blue-500/10 transition hover:-translate-y-1 hover:shadow-2xl"
                >
                    <Link :href="route('albums.show', album.id)" class="block h-full">
                        <div
                            class="relative h-48 overflow-hidden"
                            :class="[`bg-gradient-to-br ${accentClass}`]"
                            :style="album.cover ? { backgroundImage: `url(${album.cover})`, backgroundSize: 'cover', backgroundPosition: 'center' } : {}"
                        >
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/30 to-transparent" />
                            <div class="absolute left-0 top-0 flex flex-wrap gap-2 p-3">
                                <span class="rounded-full bg-white/15 px-3 py-1 text-xs text-white/90">
                                    {{ album.photos_count }} foto
                                </span>
                                <span v-if="album.owner" class="rounded-full bg-white/15 px-3 py-1 text-xs text-white/80">
                                    {{ album.owner }}
                                </span>
                            </div>
                            <div class="absolute bottom-3 left-3 right-3 flex items-end justify-between">
                                <div>
                                    <p class="text-lg font-semibold text-white drop-shadow">{{ album.title }}</p>
                                    <p class="text-xs text-white/80 truncate">{{ album.description }}</p>
                                </div>
                                <div class="flex -space-x-2">
                                    <img
                                        v-for="photo in album.photos"
                                        :key="photo.id"
                                        :src="photo.url"
                                        alt=""
                                        class="h-10 w-10 rounded-lg border border-white/20 object-cover shadow"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between px-4 py-3 text-sm text-white/70">
                            <span> Lihat album </span>
                            <span class="rounded-full bg-white/10 px-3 py-1 text-xs uppercase tracking-wide text-white/70 transition group-hover:bg-white/20">
                                Detail
                            </span>
                        </div>
                    </Link>
                </div>
                <div
                    v-if="!albums.length"
                    class="rounded-2xl border border-dashed border-white/20 bg-white/5 p-10 text-center text-white/70"
                >
                    Belum ada album. {{ canManage ? 'Buat satu untuk mulai menyimpan kenangan.' : 'Login untuk mulai menambah album.' }}
                </div>
            </div>
        </section>
    </AlbumLayout>
</template>
