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
            <p v-if="$page.props.flash?.success" class="forgot-status" role="status">
                {{ $page.props.flash.success }}
            </p>

            <form class="forgot-form" @submit.prevent="submit">

                <label class="label" for="forgot-email">{{ $t('auth.email') }}</label>
                <input id="forgot-email" v-model.trim="form.email" type="email" class="input" :placeholder="$t('auth.email')" autocomplete="email" required />
                <p v-if="form.errors.email" class="forgot-error">{{ form.errors.email }}</p>

                <button type="submit" class="btn-primary" :disabled="form.processing">
                    {{ form.processing ? $t('auth.sending_reset_link') : $t('auth.send_reset_link') }}
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
