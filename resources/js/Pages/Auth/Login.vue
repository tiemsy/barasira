<template>
    <AppLayout :title="$t('auth.login_title')">
        <section class="login-page">

            <div class="login-shell">

                <!-- LEFT VISUAL -->
                <div class="login-visual">
                    <div class="login-visual__content">
                        <span class="login-visual__badge">
                            🔐 {{ $t('ui.auth.secureBadge') }}
                        </span>

                        <h2>
                            {{ $t('ui.auth.loginHeroTitle') }}
                        </h2>

                        <p>
                            {{ $t('ui.auth.loginHeroText') }}
                        </p>

                        <div class="login-visual__features">
                            <span>
                                <DashboardIcon name="verified" />
                                {{ $t('ui.auth.verifiedProviders') }}
                            </span>

                            <span>
                                <DashboardIcon name="verified" />
                                {{ $t('ui.auth.secureMissions') }}
                            </span>

                            <span>
                                <DashboardIcon name="verified" />
                                {{ $t('ui.auth.fastSupport') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- RIGHT FORM -->
                <div class="login-panel">

                    <nav class="breadcrumb login-breadcrumb">
                        <ol>
                            <li>
                                <a href="/">
                                    {{ $t('navigation.home') }}
                                </a>
                            </li>

                            <li>
                                {{ $t('auth.login_title') }}
                            </li>
                        </ol>
                    </nav>

                    <div class="login-card">

                        <div class="login-header">
                            <span class="login-logo-dot"></span>

                            <h1 class="login-title">
                                {{ $t('auth.login_title') }}
                            </h1>

                            <p class="login-subtitle">
                                {{ $t('ui.auth.loginSubtitle') }}
                            </p>
                        </div>

                        <!-- SSO -->
                        <div class="sso-login">
                            <button type="button" class="sso-btn sso-btn--google" @click="loginWithGoogle">
                                <DashboardIcon name="google" />
                                {{ $t('ui.auth.continueGoogle') }}
                            </button>

                            <a href="/api/auth/facebook/redirect" class="sso-btn sso-btn--facebook">
                                <DashboardIcon name="facebook" />
                                {{ $t('ui.auth.continueFacebook') }}
                            </a>
                        </div>

                        <div class="auth-divider">
                            <span>{{ $t('ui.auth.loginEmailDivider') }}</span>
                        </div>

                        <form class="login-form" @submit.prevent="submit">

                            <!-- EMAIL -->
                            <div class="form-group">
                                <label for="email">
                                    {{ $t('auth.email') }}
                                </label>

                                <div class="input-wrapper">
                                    <DashboardIcon name="mail" />

                                    <input id="email" v-model="form.email" type="email" :placeholder="$t('ui.auth.emailPlaceholder')"
                                        autocomplete="email" />
                                </div>

                                <p v-if="errors.email" class="input-error">
                                    {{ errors.email[0] }}
                                </p>
                            </div>

                            <!-- PASSWORD -->
                            <div class="form-group">
                                <label for="password">
                                    {{ $t('auth.password') }}
                                </label>

                                <div class="input-wrapper">
                                    <DashboardIcon name="lock" />

                                    <input id="password" v-model="form.password"
                                        :type="showPassword ? 'text' : 'password'" :placeholder="$t('auth.password')"
                                        autocomplete="current-password" />

                                    <button type="button" class="password-toggle" @click="showPassword = !showPassword">
                                        <DashboardIcon :name="showPassword ? 'eye-off' : 'eye'" />
                                    </button>
                                </div>

                                <p v-if="errors.password" class="input-error">
                                    {{ errors.password[0] }}
                                </p>
                            </div>

                            <!-- OPTIONS -->
                            <div class="login-options">
                                <label class="remember-me">
                                    <input v-model="form.remember" type="checkbox" />

                                    <span>
                                        {{ $t('auth.remember_me') }}
                                    </span>
                                </label>

                                <a href="/forgot-password" class="forgot-link">
                                    {{ $t('ui.auth.forgotPassword') }}
                                </a>
                            </div>

                            <!-- GLOBAL ERROR -->
                            <p v-if="errors.general" class="input-error input-error--global">
                                {{ errors.general[0] }}
                            </p>

                            <!-- SUBMIT -->
                            <button type="submit" class="btn btn-primary login-submit" :disabled="form.processing">
                                <span v-if="form.processing">
                                    {{ $t('ui.auth.loggingIn') }}
                                </span>

                                <span v-else>
                                    {{ $t('navigation.login') }}
                                    <DashboardIcon name="arrow" />
                                </span>
                            </button>

                            <p class="login-footer">
                                {{ $t('ui.auth.noAccount') }}

                                <a href="/register">
                                    {{ $t('auth.no_account') }}
                                </a>
                            </p>

                        </form>

                    </div>
                </div>

            </div>

        </section>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm, usePage, router } from '@inertiajs/vue3'
import { reactive, ref } from 'vue'
import { api } from '@/lib/api'
import axios from 'axios'
import DashboardIcon from '@/Components/DashboardIcon.vue'
import { useI18n } from 'vue-i18n'

const baseURL = import.meta.env.VITE_API_URL

const errors = ref({})
const loading = ref(false)
const { t } = useI18n()

const form = reactive({
    email: '',
    password: '',
    remember: false,
})

const loginWithGoogle = () => {
    window.location.href =
        `${import.meta.env.VITE_API_URL}/api/auth/google/redirect?intent=login`
}

const submit = async () => {
    errors.value = {}
    loading.value = true

    try {
        await axios.get(`${baseURL}/sanctum/csrf-cookie`, {
            withCredentials: true,
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        const res = await api.post('/login', form)

        // console.log(res.data);

        window.location.href = res.data.redirect
        // router.push(res.data.redirect)
    } catch (e) {
        console.error('Erreur login:', e.response?.status, e.response?.data)

        if (e.response?.status === 422) {
            errors.value = e.response.data.errors || {}
            //
        } else if (e.response?.status === 403) {
            errors.value.general = [t('ui.auth.verifyEmail')]
            router.push('/email/verify')
        }
        else {
            errors.value.general = [
                e.response?.data?.message || t('ui.auth.serverError')
            ]
        }
    } finally {
        loading.value = false
    }
}
</script>
