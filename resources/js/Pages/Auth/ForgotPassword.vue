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

<style scoped>
.forgot-container {
    max-width: 480px;
    margin: 60px auto;
    padding: 40px;
    background: #ffffff;
    border-radius: 14px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    text-align: center;
}

.forgot-title {
    font-size: 26px;
    font-weight: 700;
    color: #222;
    margin-bottom: 10px;
}

.forgot-text {
    font-size: 16px;
    color: #555;
    margin-bottom: 25px;
}

.label {
    display: block;
    text-align: left;
    margin-bottom: 6px;
    font-weight: 600;
}

.input {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    margin-bottom: 20px;
}

.btn-primary {
    width: 100%;
    padding: 14px;
    background: #0d6efd;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
}

.btn-primary:hover {
    background: #0b5ed7;
}

.btn-link {
    background: none;
    border: none;
    color: #0d6efd;
    font-size: 15px;
    cursor: pointer;
}

.btn-link:hover {
    text-decoration: underline;
}
</style>
