<template>
    <AppLayout :title="$t('auth.reset_password_title')">
        <Head :title="$t('auth.reset_password_title')" />
        <main class="forgot-page">
        <section class="forgot-container">
            <div class="forgot-icon" aria-hidden="true"><DashboardIcon name="lock" /></div>
            <header class="forgot-header">
                <span>{{ $t('ui.auth.secureBadge') }}</span>
                <h1 class="forgot-title">{{ $t('auth.reset_password_title') }}</h1>
                <p class="forgot-text">{{ $t('auth.reset_password_text') }}</p>
            </header>

            <form class="forgot-form" @submit.prevent="submit">
                <label class="label" for="reset-email">{{ $t('auth.email') }}</label>
                <input id="reset-email" v-model.trim="form.email" type="email" class="input" autocomplete="email" required>
                <p v-if="form.errors.email" class="forgot-error">{{ form.errors.email }}</p>

                <label class="label" for="reset-password">{{ $t('auth.new_password') }}</label>
                <input id="reset-password" v-model="form.password" type="password" class="input" autocomplete="new-password" required>
                <p v-if="form.errors.password" class="forgot-error">{{ form.errors.password }}</p>

                <label class="label" for="reset-password-confirmation">{{ $t('auth.password_confirmation') }}</label>
                <input id="reset-password-confirmation" v-model="form.password_confirmation" type="password" class="input" autocomplete="new-password" required>

                <button type="submit" class="btn-primary" :disabled="form.processing">
                    {{ form.processing ? $t('auth.resetting_password') : $t('auth.reset_password') }}
                </button>
            </form>

            <button class="btn-link" type="button" @click="router.visit('/login')">
                {{ $t('auth.back_to_login') }}
            </button>
        </section>
        </main>
    </AppLayout>
</template>

<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import DashboardIcon from '@/Components/DashboardIcon.vue'

const props = defineProps({
    token: { type: String, required: true },
    email: { type: String, default: '' },
})

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
})

const submit = () => form.post('/reset-password')
</script>

<style scoped lang="scss" src="../../../scss/pages/auth/_forgot-password.scss"></style>
