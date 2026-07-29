<template>
    <AppLayout :title="$t('auth.forgot_password_title')">
        <Head :title="$t('auth.forgot_password_title')" />
        <main class="forgot-page">
            <section class="forgot-container">
                <div class="forgot-icon" aria-hidden="true">
                    <DashboardIcon name="lock" />
                </div>

                <header class="forgot-header">
                    <span>{{ $t('ui.auth.secureBadge') }}</span>
                    <h1 class="forgot-title">{{ $t('auth.forgot_password_title') }}</h1>
                    <p class="forgot-text">{{ $t('auth.forgot_password_text') }}</p>
                </header>

                <p v-if="$page.props.flash?.success" class="forgot-status" role="status">
                    <DashboardIcon name="verified" />
                    <span>{{ $page.props.flash.success }}</span>
                </p>

                <form class="forgot-form" @submit.prevent="submit">
                    <label class="label" for="forgot-email">{{ $t('auth.email') }}</label>
                    <div class="forgot-input">
                        <DashboardIcon name="mail" />
                        <input id="forgot-email" v-model.trim="form.email" type="email" :placeholder="$t('ui.auth.emailPlaceholder')" autocomplete="email" required>
                    </div>
                    <p v-if="form.errors.email" class="forgot-error">{{ form.errors.email }}</p>

                    <button type="submit" class="forgot-submit" :disabled="form.processing">
                        <span>{{ form.processing ? $t('auth.sending_reset_link') : $t('auth.send_reset_link') }}</span>
                        <DashboardIcon v-if="!form.processing" name="arrow" />
                    </button>
                </form>

                <Link href="/login" class="forgot-back">
                    <span aria-hidden="true">←</span>
                    {{ $t('auth.back_to_login') }}
                </Link>
            </section>
        </main>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import DashboardIcon from '@/Components/DashboardIcon.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
    email: ''
})

const submit = () => {
    form.post('/forgot-password')
}
</script>

<style scoped lang="scss" src="../../../scss/pages/auth/_forgot-password.scss"></style>
