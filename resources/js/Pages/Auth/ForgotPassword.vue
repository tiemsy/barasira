<template>
    <AppLayout>

        <div class="forgot-container">

            <!-- Title -->
            <h1 class="forgot-title">
                {{ $t('auth.forgot_password_title') }}
            </h1>

            <!-- Description -->
            <p class="forgot-text">
                {{ $t('auth.forgot_password_text') }}
            </p>

            <!-- Form -->
            <form @submit.prevent="submit" class="forgot-form">

                <label class="label">{{ $t('auth.email') }}</label>
                <input type="email" v-model="form.email" class="input" :placeholder="$t('auth.email')" />

                <button class="btn-primary" :disabled="form.processing">
                    {{ $t('auth.send_reset_link') }}
                </button>
            </form>

            <!-- Back to login -->
            <button class="btn-link mt-4" @click="goLogin">
                {{ $t('auth.back_to_login') }}
            </button>

        </div>

    </AppLayout>
</template>

<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm, router } from '@inertiajs/vue3'

const form = useForm({
    email: ''
})

const submit = () => {
    form.post('/forgot-password')
}

const goLogin = () => {
    router.visit('/login')
}
</script>

<style scoped lang="scss" src="../../../scss/pages/auth/_forgot-password.scss"></style>
