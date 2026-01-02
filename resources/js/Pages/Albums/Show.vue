<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import AlbumLayout from '@/Layouts/AlbumLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import LazyImage from '@/Components/LazyImage.vue';
import { useLoading } from '@/composables/useLoading';

const props = defineProps({
    album: {
        type: Object,
        required: true,
    },
    canUpload: {
        type: Boolean,
        default: false,
    },
});

const uploadForm = useForm({
    title: '',
    caption: '',
    taken_at: '',
    photos: [],
});

const { startLoading, stopLoading } = useLoading();

const photoInput = ref(null);
const showUploadModal = ref(false);
const editForm = useForm({
    title: props.album.title || '',
    description: props.album.description || '',
});

const submitPhoto = () => {
    startLoading('Mengupload foto...');
    uploadForm.post(route('albums.photos.store', props.album.token), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            uploadForm.reset();
            if (photoInput.value) {
                photoInput.value.value = null;
            }
            showUploadModal.value = false;
            stopLoading();
        },
        onError: () => stopLoading(),
    });
};

const handleFileChange = (event) => {
    uploadForm.photos = Array.from(event.target.files);
};

const deleteForm = useForm({});

const showConfirm = ref(false);
const targetPhotoId = ref(null);
const showPreview = ref(false);
const previewPhoto = ref(null);
const activeTab = ref('gallery');

const confirmDelete = (photoId) => {
    targetPhotoId.value = photoId;
    showConfirm.value = true;
};

const deletePhoto = () => {
    if (!targetPhotoId.value) return;
    startLoading('Menghapus foto...');
    deleteForm.delete(route('albums.photos.destroy', { albumToken: props.album.token, photo: targetPhotoId.value }), {
        preserveScroll: true,
        onFinish: () => {
            showConfirm.value = false;
            targetPhotoId.value = null;
            stopLoading();
        },
    });
};

const openPreview = (photo) => {
    previewPhoto.value = photo;
    showPreview.value = true;
};

const closePreview = () => {
    showPreview.value = false;
    previewPhoto.value = null;
};

const openUploadModal = () => {
    showUploadModal.value = true;
};

const closeUploadModal = () => {
    showUploadModal.value = false;
};

const submitEdit = () => {
    startLoading('Menyimpan perubahan...');
    editForm.put(route('albums.update', { albumToken: props.album.token }), {
        preserveScroll: true,
        onSuccess: () => stopLoading(),
        onError: () => stopLoading(),
    });
};
</script>

<template>
    <Head :title="album.title" />
    <AlbumLayout>
        <section class="mb-6 space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <Link
                    :href="route('albums.index')"
                    class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-slate-950/80 px-3 py-2 text-sm font-medium text-white/90 shadow-lg shadow-blue-500/10 transition hover:-translate-y-0.5 hover:border-white/40 hover:text-white"
                >
                    Kembali Ke Daftar Album
                </Link>
                <div class="flex flex-wrap items-center gap-2 text-xs uppercase tracking-[0.25em] text-white/70">
                    <span class="rounded-full bg-white/10 px-3 py-1">{{ album.title }}</span>
                    <span v-if="album.description" class="rounded-full bg-white/10 px-3 py-1">{{ album.description }}</span>
                </div>
            </div>
            <div class="flex w-full flex-wrap items-center justify-end gap-1 text-[10px] text-white/75">
                <span class="inline-flex items-center gap-1 rounded-full border border-white/20 bg-slate-950/80 px-2 py-0.5 shadow-md shadow-blue-500/10 backdrop-blur">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400" />
                    {{ album.photos?.length || 0 }} Foto
                </span>
            </div>
        </section>


        <section class="mt-8">
            <div class="mb-4 flex flex-wrap items-center justify-center gap-3">
                <div class="flex gap-2 rounded-full border border-white/10 bg-white/5 p-1 text-sm text-white/80">
                    <button
                        type="button"
                        class="rounded-full px-4 py-2 transition"
                        :class="activeTab === 'gallery' ? 'bg-white/15 text-white shadow-lg shadow-blue-500/10' : 'text-white/70'"
                        @click="activeTab = 'gallery'"
                    >
                        Gallery
                    </button>
                    <button
                        type="button"
                        class="rounded-full px-4 py-2 transition"
                        :class="activeTab === 'edit' ? 'bg-white/15 text-white shadow-lg shadow-blue-500/10' : 'text-white/70'"
                        @click="activeTab = 'edit'"
                    >
                        Ubah Detail Album
                    </button>
                </div>
            </div>
            <Transition name="fade-swap" mode="out-in">
                <div v-if="activeTab === 'gallery'" key="gallery">
                    <div v-if="album.photos?.length" class="columns-1 sm:columns-2 lg:columns-3 [column-gap:1.25rem] space-y-4 sm:space-y-6">
                        <article
                            v-for="photo in album.photos"
                            :key="photo.id"
                            class="group relative mb-4 break-inside-avoid overflow-hidden rounded-3xl border border-white/10 bg-slate-950/70 shadow-2xl shadow-blue-500/10 backdrop-blur transition hover:-translate-y-1 hover:shadow-blue-500/20 sm:mb-6 cursor-pointer"
                            @click="openPreview(photo)"
                        >
                            <LazyImage
                                :src="photo.url"
                                :alt="photo.title || 'Foto keluarga'"
                                loading-text="Memuat foto..."
                                error-text="Gagal memuat foto"
                                image-class="w-full object-cover transition duration-700 group-hover:scale-105"
                                container-class="relative overflow-hidden"
                            >
                                <div class="absolute inset-0 bg-gradient-to-b from-slate-950/30 via-slate-950/20 to-slate-950/80" />

                                <div class="absolute left-3 right-3 top-3 flex items-center justify-between gap-2 text-[11px] text-white">
                                    <div class="flex items-center gap-2 rounded-full border border-white/15 bg-slate-950/75 px-3 py-1 shadow-lg shadow-black/30 backdrop-blur">
                                        <span class="h-2 w-2 rounded-full bg-emerald-400" />
                                        <span class="uppercase tracking-wide text-white/90">{{ photo.taken_at || 'Tanpa tanggal' }}</span>
                                    </div>
                                    <button
                                        v-if="canUpload"
                                        type="button"
                                        class="rounded-full border border-white/20 bg-gradient-to-r from-rose-500 to-red-500 px-3 py-1 text-[11px] font-semibold text-white shadow-lg shadow-rose-500/30 transition hover:-translate-y-0.5"
                                        @click.stop="confirmDelete(photo.id)"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </LazyImage>

                            <div class="p-3 text-white">
                                <p class="text-sm font-semibold leading-snug line-clamp-1">{{ photo.title || 'Foto' }}</p>
                                <p class="mt-1 text-xs text-white/70 line-clamp-2">
                                    {{ photo.caption || 'Cerita singkat belum ditambahkan.' }}
                                </p>
                                <div class="mt-2 flex items-center justify-between text-xs text-white/70">
                                    <div class="flex items-center gap-1.5">
                                        <span class="h-2 w-2 rounded-full bg-emerald-400" />
                                        <span>Oleh {{ photo.uploader || 'Pengguna' }}</span>
                                    </div>
                                    <span class="rounded-full bg-white/10 px-2.5 py-1 text-[11px] uppercase tracking-wide">
                                        Album {{ album.title }}
                                    </span>
                                </div>
                            </div>
                        </article>
                    </div>
                    <div v-else class="rounded-2xl border border-dashed border-white/15 bg-white/5 p-10 text-center text-white/70">
                        Belum ada foto. {{ canUpload ? 'Unggah foto pertamamu!' : 'Login untuk menambahkan foto.' }}
                    </div>
                </div>

                <div v-else key="edit" class="rounded-2xl border border-white/10 bg-slate-950/70 p-5 shadow-2xl shadow-blue-500/10 backdrop-blur">
                    <h3 class="text-lg font-semibold text-white">Ubah album</h3>
                    <p class="text-sm text-white/65">Perbarui judul dan deskripsi, lalu simpan.</p>
                    <form @submit.prevent="submitEdit" class="mt-4 space-y-3">
                        <div>
                            <InputLabel for="edit-title-tab" value="Judul" />
                            <TextInput
                                id="edit-title-tab"
                                v-model="editForm.title"
                                type="text"
                                class="mt-2 block w-full"
                                required
                            />
                            <InputError :message="editForm.errors.title" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel for="edit-description-tab" value="Deskripsi" />
                            <textarea
                                id="edit-description-tab"
                                v-model="editForm.description"
                                rows="3"
                                class="mt-2 w-full rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2 text-sm text-white focus:border-cyan-400 focus:ring-0"
                            />
                            <InputError :message="editForm.errors.description" class="mt-1" />
                        </div>
                        <PrimaryButton :disabled="editForm.processing">
                            Simpan perubahan
                        </PrimaryButton>
                    </form>
                </div>
            </Transition>
        </section>

        <button
            v-if="canUpload"
            type="button"
            class="fixed bottom-6 right-6 z-40 flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-r from-cyan-500 to-blue-600 text-2xl font-bold text-white shadow-2xl shadow-cyan-500/30 transition hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:ring-offset-2 focus:ring-offset-slate-900"
            @click="openUploadModal"
            aria-label="Tambah foto"
        >
            +
        </button>

        <Modal :show="showUploadModal" maxWidth="lg" @close="closeUploadModal">
            <div class="bg-slate-950 text-white">
                <div class="flex items-center justify-between border-b border-white/10 px-4 py-3">
                    <div>
                        <h3 class="text-lg font-semibold">Tambah foto</h3>
                        <p class="text-sm text-white/70">Unggah foto baru ke album ini.</p>
                    </div>
                    <button
                        type="button"
                        class="rounded-full border border-white/20 px-3 py-1 text-xs text-white/80 hover:border-white/40 hover:text-white"
                        @click="closeUploadModal"
                    >
                        Tutup
                    </button>
                </div>
                <div class="px-4 py-5">
                    <form @submit.prevent="submitPhoto" class="space-y-4">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <InputLabel for="title-modal" value="Judul (opsional)" />
                                <TextInput
                                    id="title-modal"
                                    v-model="uploadForm.title"
                                    type="text"
                                    class="mt-2 block w-full"
                                    placeholder="Makan malam keluarga"
                                />
                                <InputError :message="uploadForm.errors.title" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="taken_at_modal" value="Tanggal" />
                                <TextInput
                                    id="taken_at_modal"
                                    v-model="uploadForm.taken_at"
                                    type="date"
                                    class="mt-2 block w-full"
                                />
                                <InputError :message="uploadForm.errors.taken_at" class="mt-2" />
                            </div>
                        </div>
                        <div>
                            <InputLabel for="caption-modal" value="Cerita singkat" />
                            <textarea
                                id="caption-modal"
                                v-model="uploadForm.caption"
                                rows="3"
                                class="mt-2 w-full rounded-xl border border-white/10 bg-slate-900/60 px-3 py-2 text-sm text-white focus:border-cyan-400 focus:ring-0"
                                placeholder="Tuliskan momen istimewa..."
                            />
                            <InputError :message="uploadForm.errors.caption" class="mt-2" />
                        </div>
                        <div class="flex flex-col gap-2 rounded-2xl border border-dashed border-white/20 bg-slate-900/60 p-4 text-sm text-white/80">
                            <div class="flex items-center justify-between">
                                <InputLabel for="photo-modal" value="File foto (pilih beberapa)" />
                                <span class="rounded-full bg-white/10 px-3 py-1 text-[11px] uppercase tracking-wide text-white/70">max 5MB</span>
                            </div>
                            <input
                                id="photo-modal"
                                ref="photoInput"
                                type="file"
                                accept="image/*"
                                multiple
                                class="w-full text-sm text-white file:mr-4 file:rounded-lg file:border-0 file:bg-cyan-500 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-cyan-600"
                                @change="handleFileChange"
                            />
                            <InputError :message="uploadForm.errors.photos" class="mt-1" />
                        </div>
                        <PrimaryButton :disabled="uploadForm.processing" class="w-full justify-center">
                            Upload {{ uploadForm.photos.length > 0 ? uploadForm.photos.length + ' Foto' : 'Foto' }}
                        </PrimaryButton>
                    </form>
                </div>
            </div>
        </Modal>

        <Modal :show="showPreview" maxWidth="2xl" @close="closePreview">
            <div class="bg-slate-950 text-white">
                <div class="flex items-center justify-between border-b border-white/10 px-4 py-3">
                    <div>
                        <p class="text-sm font-semibold">{{ previewPhoto?.title || 'Foto' }}</p>
                        <p class="text-[11px] text-white/70">{{ previewPhoto?.taken_at || 'Tanpa tanggal' }}</p>
                    </div>
                    <button
                        type="button"
                        class="rounded-full border border-white/20 px-3 py-1 text-xs text-white/80 hover:border-white/40 hover:text-white"
                        @click="closePreview"
                    >
                        Tutup
                    </button>
                </div>
                <div class="max-h-[70vh] overflow-auto bg-slate-900">
                    <LazyImage
                        v-if="previewPhoto?.url"
                        :src="previewPhoto.url"
                        :alt="previewPhoto?.title || 'Foto'"
                        loading-text="Memuat preview..."
                        error-text="Gagal memuat preview"
                        image-class="mx-auto max-h-[70vh] w-full object-contain"
                        container-class="relative"
                        :lazy="false"
                    />
                </div>
                <div class="space-y-1 border-t border-white/10 px-4 py-3 text-xs text-white/75">
                    <p class="line-clamp-2">{{ previewPhoto?.caption || 'Cerita singkat belum ditambahkan.' }}</p>
                    <p class="text-white/60">Oleh {{ previewPhoto?.uploader || 'Pengguna' }}</p>
                </div>
            </div>
        </Modal>

        <Modal :show="showConfirm" maxWidth="md" @close="showConfirm = false">
            <div class="bg-slate-950 text-white">
                <div class="flex items-start gap-3 border-b border-white/10 px-6 py-4">
                    <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-2xl bg-rose-500/20 text-rose-200">
                        !
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">Hapus foto?</h3>
                        <p class="text-sm text-white/70">Foto akan dihapus permanen dari album ini.</p>
                    </div>
                </div>
                <div class="px-6 py-5 text-sm text-white/80">
                    Tindakan ini tidak bisa dibatalkan. Pastikan foto sudah disalin bila perlu.
                </div>
                <div class="flex items-center justify-end gap-3 border-t border-white/10 bg-slate-900/80 px-6 py-4">
                    <button
                        type="button"
                        class="rounded-full border border-white/20 px-4 py-2 text-sm text-white/80 transition hover:-translate-y-0.5 hover:border-white/40 hover:text-white"
                        @click="showConfirm = false"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        class="rounded-full bg-gradient-to-r from-rose-500 to-red-500 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-rose-500/20 transition hover:-translate-y-0.5"
                        @click="deletePhoto"
                        :disabled="deleteForm.processing"
                    >
                        Hapus
                    </button>
                </div>
            </div>
        </Modal>
    </AlbumLayout>
</template>

<style scoped>
.fade-swap-enter-active,
.fade-swap-leave-active {
    transition: opacity 200ms ease, transform 200ms ease;
}

.fade-swap-enter-from,
.fade-swap-leave-to {
    opacity: 0;
    transform: translateY(6px);
}
</style>
