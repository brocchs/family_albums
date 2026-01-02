<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useLoading } from '@/composables/useLoading';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const { startLoading, stopLoading } = useLoading();

const submit = () => {
    startLoading('Masuk ke akun...');
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
            stopLoading();
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Masuk" />

        <div class="space-y-6">
            <div>
                <h1 class="text-2xl font-bold text-white">Masuk ke akun</h1>
                <p class="mt-1 text-sm text-white/70">Akses album keluarga dengan kredensial kamu.</p>
            </div>

            <div v-if="status" class="rounded-xl border border-emerald-300/30 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-100">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <InputLabel for="email" value="Email" class="text-white" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-2 block w-full text-white"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="nama@email.com"
                    />
                    <InputError class="mt-2 text-rose-300" :message="form.errors.email" />
                </div>

                <div>
                    <InputLabel for="password" value="Kata sandi" class="text-white" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-2 block w-full text-white"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="********"
                    />
                    <InputError class="mt-2 text-rose-300" :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 text-white/80">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        Ingat saya
                    </label>
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-white/70 underline transition hover:text-white"
                    >
                        Lupa kata sandi?
                    </Link>
                </div>

                <PrimaryButton class="w-full justify-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Masuk
                </PrimaryButton>
            </form>
        </div>
    </GuestLayout>
</template>
