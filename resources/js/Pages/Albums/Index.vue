<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AlbumLayout from '@/Layouts/AlbumLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Modal from '@/Components/Modal.vue';
import { useLoading } from '@/composables/useLoading';

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

const deleteForm = useForm({});
const showDeleteModal = ref(false);
const albumToDelete = ref(null);

const { startLoading, stopLoading } = useLoading();

const accentClass = computed(() => 'from-cyan-400 via-blue-500 to-indigo-500');

const createAlbum = () => {
    startLoading('Membuat album...');
    albumForm.post(route('albums.store'), {
        preserveScroll: true,
        onSuccess: () => {
            albumForm.reset();
            stopLoading();
        },
        onError: () => stopLoading(),
    });
};

const confirmDeleteAlbum = (album) => {
    albumToDelete.value = album;
    showDeleteModal.value = true;
};

const deleteAlbum = () => {
    if (!albumToDelete.value) return;
    startLoading('Menghapus album...');
    deleteForm.delete(route('albums.destroy', albumToDelete.value.token), {
        onFinish: () => {
            showDeleteModal.value = false;
            albumToDelete.value = null;
            stopLoading();
        },
    });
};
</script>

<template>
    <Head title="Album Keluarga" />
    <AlbumLayout>
        <section class="grid gap-10 lg:grid-cols-[1.4fr,0.8fr] lg:items-start">
            <div class="space-y-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.28em] text-white/60">Albums</p>
                        <h1 class="text-4xl font-bold text-white sm:text-5xl">Semua album saya.</h1>
                    </div>
                    <div class="rounded-full bg-white/10 px-4 py-2 text-xs font-semibold text-white/80">
                        {{ albums.length }} album
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-xl border border-white/10 bg-white/5 p-4 shadow-lg shadow-cyan-500/10">
                        <p class="text-xs text-white/60">Foto total</p>
                        <p class="text-2xl font-semibold text-white">{{ albums.reduce((t, a) => t + (a.photos_count || 0), 0) }}</p>
                        <p class="text-sm text-white/60">Gabungan semua album.</p>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-white/5 p-4 shadow-lg shadow-blue-500/10">
                        <p class="text-xs text-white/60">Album favorit</p>
                        <p class="text-lg font-semibold text-white">Pilih cepat</p>
                        <p class="text-sm text-white/60">Klik kartu untuk buka.</p>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-white/5 p-4 shadow-lg shadow-emerald-500/10">
                        <p class="text-xs text-white/60">Akses</p>
                        <p class="text-lg font-semibold text-white">Hanya login</p>
                        <p class="text-sm text-white/60">Tidak ada registrasi publik.</p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="album in albums"
                        :key="album.id"
                        :href="route('albums.show', album.token)"
                        class="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/5 shadow-xl shadow-blue-500/10 transition hover:-translate-y-1 hover:shadow-2xl"
                    >
                        <div
                            class="relative h-44 overflow-hidden"
                            :class="[`bg-gradient-to-br ${accentClass}`]"
                            :style="album.cover ? { backgroundImage: `url(${album.cover})`, backgroundSize: 'cover', backgroundPosition: 'center' } : {}"
                        >
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/30 to-transparent" />
                            <div class="absolute left-0 top-0 flex flex-wrap gap-2 p-3">
                                <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-slate-950/80 px-3 py-1 text-[11px] uppercase tracking-wide text-white shadow-lg shadow-black/25 backdrop-blur">
                                    <span class="h-2 w-2 rounded-full bg-emerald-400" />
                                    {{ album.photos_count }} foto
                                </span>
                                <span
                                    v-if="album.owner"
                                    class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-slate-950/80 px-3 py-1 text-[11px] text-white shadow-lg shadow-black/25 backdrop-blur"
                                >
                                    <span class="h-2 w-2 rounded-full bg-cyan-400" />
                                    {{ album.owner }}
                                </span>
                            </div>
                            <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between gap-3">
                                <div class="rounded-2xl border border-white/10 bg-slate-950/70 px-3 py-2 shadow-lg shadow-blue-500/10 backdrop-blur">
                                    <p class="text-base font-semibold text-white drop-shadow">{{ album.title }}</p>
                                    <p class="text-[11px] text-white/70 line-clamp-1">{{ album.description || 'Belum ada deskripsi.' }}</p>
                                </div>
                                <div class="flex -space-x-2">
                                    <img
                                        v-for="photo in album.photos"
                                        :key="photo.id"
                                        :src="photo.url"
                                        alt=""
                                        class="h-9 w-9 rounded-lg border border-white/20 object-cover shadow"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between px-4 py-3 text-xs text-white/70">
                            <span class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-emerald-400" /> Buka album
                            </span>
                            <button
                                v-if="canManage"
                                type="button"
                                class="rounded-full bg-gradient-to-r from-rose-500 to-red-500 px-3 py-1 text-[11px] font-semibold text-white shadow-lg shadow-rose-500/20 transition hover:-translate-y-0.5"
                                @click.prevent="confirmDeleteAlbum(album)"
                            >
                                Hapus
                            </button>
                        </div>
                    </Link>

                    <div
                        v-if="!albums.length"
                        class="rounded-2xl border border-dashed border-white/20 bg-white/5 p-10 text-center text-white/70"
                    >
                        Belum ada album. {{ canManage ? 'Buat satu untuk mulai menyimpan kenangan.' : 'Login untuk mulai menambah album.' }}
                    </div>
                </div>
            </div>

            <div v-if="canManage" class="rounded-2xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-cyan-500/10 backdrop-blur">
                <h3 class="mb-1 text-lg font-semibold text-white">Buat album baru</h3>
                <p class="mb-4 text-sm text-white/70">Isi judul dan deskripsi singkat, selesai.</p>
                <form @submit.prevent="createAlbum" class="space-y-4">
                    <div>
                        <InputLabel for="title" value="Judul Album" />
                        <TextInput
                            id="title"
                            v-model="albumForm.title"
                            type="text"
                            class="mt-2 block w-full"
                            placeholder="Liburan keluarga, dst."
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
                            placeholder="Catatan pendek tentang album ini..."
                        />
                        <InputError :message="albumForm.errors.description" class="mt-2" />
                    </div>
                    <PrimaryButton :disabled="albumForm.processing">
                        Simpan Album
                    </PrimaryButton>
                </form>
            </div>
        </section>
        
        <Modal :show="showDeleteModal" maxWidth="md" @close="showDeleteModal = false">
            <div class="bg-slate-950 text-white">
                <div class="flex items-start gap-3 border-b border-white/10 px-6 py-4">
                    <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-2xl bg-rose-500/20 text-rose-200">
                        !
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">Hapus Album?</h3>
                        <p class="text-sm text-white/70">Album "{{ albumToDelete?.title }}" akan dihapus permanen.</p>
                    </div>
                </div>
                <div class="px-6 py-5 text-sm text-white/80">
                    <p class="mb-3">Tindakan ini akan menghapus:</p>
                    <ul class="list-disc list-inside space-y-1 text-white/70">
                        <li>Album dan semua informasinya</li>
                        <li>Semua foto di dalam album ({{ albumToDelete?.photos_count || 0 }} foto)</li>
                        <li>Semua data tidak dapat dikembalikan</li>
                    </ul>
                </div>
                <div class="flex items-center justify-end gap-3 border-t border-white/10 bg-slate-900/80 px-6 py-4">
                    <button
                        type="button"
                        class="rounded-full border border-white/20 px-4 py-2 text-sm text-white/80 transition hover:-translate-y-0.5 hover:border-white/40 hover:text-white"
                        @click="showDeleteModal = false"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        class="rounded-full bg-gradient-to-r from-rose-500 to-red-500 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-rose-500/20 transition hover:-translate-y-0.5"
                        @click="deleteAlbum"
                        :disabled="deleteForm.processing"
                    >
                        Hapus Album
                    </button>
                </div>
            </div>
        </Modal>
    </AlbumLayout>
</template>
