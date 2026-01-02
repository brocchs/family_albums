<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AlbumLayout from '@/Layouts/AlbumLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

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
    photo: null,
});

const photoInput = ref(null);

const submitPhoto = () => {
    uploadForm.post(route('albums.photos.store', props.album.id), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            uploadForm.reset();
            if (photoInput.value) {
                photoInput.value.value = null;
            }
        },
    });
};

const handleFileChange = (event) => {
    uploadForm.photo = event.target.files[0];
};
</script>

<template>
    <Head :title="album.title" />
    <AlbumLayout>
        <section
            class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900/60 via-slate-900/80 to-slate-950 shadow-2xl shadow-blue-500/10"
        >
            <div
                v-if="album.cover"
                class="absolute inset-0 opacity-40"
                :style="{ backgroundImage: `url(${album.cover})`, backgroundSize: 'cover', backgroundPosition: 'center' }"
            />
            <div class="relative grid gap-6 p-8 lg:grid-cols-[2fr,1fr] lg:gap-10">
                <div class="space-y-4">
                    <Link href="/" class="inline-flex items-center gap-2 text-sm text-white/70 hover:text-white">
                        ← Kembali ke album
                    </Link>
                    <h1 class="text-3xl font-bold text-white sm:text-4xl">{{ album.title }}</h1>
                    <p class="text-white/80">
                        {{ album.description || 'Kisah keluarga yang bisa kita simpan di sini.' }}
                    </p>
                    <div class="flex flex-wrap gap-3 text-sm text-white/80">
                        <span class="rounded-full bg-white/10 px-4 py-2"> {{ album.photos?.length || 0 }} foto </span>
                        <span v-if="album.owner" class="rounded-full bg-white/10 px-4 py-2"> Dibuat oleh {{ album.owner }} </span>
                        <span class="rounded-full bg-white/10 px-4 py-2"> Upload membutuhkan login </span>
                    </div>
                </div>

                <div v-if="canUpload" class="rounded-2xl border border-white/10 bg-white/10 p-5 backdrop-blur">
                    <h3 class="mb-3 text-lg font-semibold text-white">Tambahkan foto</h3>
                    <form @submit.prevent="submitPhoto" class="space-y-3">
                        <div>
                            <InputLabel for="title" value="Judul (opsional)" />
                            <TextInput
                                id="title"
                                v-model="uploadForm.title"
                                type="text"
                                class="mt-2 block w-full"
                                placeholder="Makan malam keluarga"
                            />
                            <InputError :message="uploadForm.errors.title" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="caption" value="Cerita singkat" />
                            <textarea
                                id="caption"
                                v-model="uploadForm.caption"
                                rows="2"
                                class="mt-2 w-full rounded-lg border border-white/10 bg-slate-950/40 px-3 py-2 text-sm text-white focus:border-cyan-400 focus:ring-0"
                                placeholder="Tuliskan momen istimewa..."
                            />
                            <InputError :message="uploadForm.errors.caption" class="mt-2" />
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <InputLabel for="taken_at" value="Tanggal" />
                                <TextInput
                                    id="taken_at"
                                    v-model="uploadForm.taken_at"
                                    type="date"
                                    class="mt-2 block w-full"
                                />
                                <InputError :message="uploadForm.errors.taken_at" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="photo" value="File foto" />
                                <input
                                    id="photo"
                                    ref="photoInput"
                                    type="file"
                                    accept="image/*"
                                    class="mt-2 block w-full text-sm text-white file:mr-4 file:rounded-lg file:border-0 file:bg-cyan-500 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-cyan-600"
                                    @change="handleFileChange"
                                />
                                <InputError :message="uploadForm.errors.photo" class="mt-2" />
                            </div>
                        </div>
                        <PrimaryButton :disabled="uploadForm.processing">
                            Upload Foto
                        </PrimaryButton>
                    </form>
                </div>
            </div>
        </section>

        <section class="mt-8">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-semibold text-white">Isi album</h2>
                <p class="text-sm text-white/60">Ditampilkan terbaru dahulu</p>
            </div>
            <div v-if="album.photos?.length" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <article
                    v-for="photo in album.photos"
                    :key="photo.id"
                    class="group overflow-hidden rounded-2xl border border-white/10 bg-white/5 shadow-lg shadow-blue-500/10"
                >
                    <div class="relative h-52 overflow-hidden">
                        <img
                            :src="photo.url"
                            :alt="photo.title || 'Foto keluarga'"
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-slate-950/20 to-transparent" />
                        <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between text-white drop-shadow">
                            <div>
                                <p class="text-lg font-semibold">{{ photo.title || 'Foto' }}</p>
                                <p class="text-xs text-white/80">
                                    {{ photo.caption || 'Cerita singkat belum ditambahkan.' }}
                                </p>
                            </div>
                            <div class="rounded-full bg-white/15 px-3 py-1 text-[11px] uppercase tracking-wide">
                                {{ photo.taken_at || 'Tanpa tanggal' }}
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between px-4 py-3 text-xs text-white/60">
                        <span>Diunggah oleh {{ photo.uploader || 'Pengguna' }}</span>
                        <span class="rounded-full bg-white/10 px-3 py-1">Album {{ album.title }}</span>
                    </div>
                </article>
            </div>
            <div v-else class="rounded-2xl border border-dashed border-white/15 bg-white/5 p-10 text-center text-white/70">
                Belum ada foto. {{ canUpload ? 'Unggah foto pertamamu!' : 'Login untuk menambahkan foto.' }}
            </div>
        </section>
    </AlbumLayout>
</template>
