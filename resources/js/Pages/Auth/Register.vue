<template>
    <AppLayout :title="$t('auth.register_title')">
        <section class="register-page">

            <div class="register-shell">

                <!-- LEFT VISUAL -->
                <div class="register-visual">
                    <div class="register-visual__content">

                        <span class="register-visual__badge">
                            🚀 {{ $t('ui.auth.joinBadge') }}
                        </span>

                        <h2>
                            {{ $t('ui.auth.registerHeroTitle') }}
                        </h2>

                        <p>
                            {{ $t('ui.auth.registerHeroText') }}
                        </p>

                        <div class="register-visual__features">
                            <span>
                                <DashboardIcon name="verified" />
                                {{ $t('ui.auth.verifiedProfile') }}
                            </span>

                            <span>
                                <DashboardIcon name="verified" />
                                {{ $t('ui.auth.missionsServices') }}
                            </span>

                            <span>
                                <DashboardIcon name="verified" />
                                {{ $t('ui.auth.integratedMessaging') }}
                            </span>
                        </div>

                    </div>
                </div>

                <!-- RIGHT FORM -->
                <div class="register-panel">

                    <nav class="breadcrumb register-breadcrumb">
                        <ol>
                            <li>
                                <a href="/">
                                    {{ $t('navigation.home') }}
                                </a>
                            </li>

                            <li>
                                {{ $t('navigation.register') }}
                            </li>
                        </ol>
                    </nav>

                    <div class="register-card">

                        <div class="register-header">
                            <span class="register-logo-dot"></span>

                            <h1 class="register-title">
                                {{ $t('auth.register_title') }}
                            </h1>

                            <p class="register-subtitle">
                                {{ $t('ui.auth.registerSubtitle') }}
                            </p>
                        </div>

                        <!-- SSO -->
                        <div class="sso-login">
                            <a
                                href="/api/auth/google/redirect?intent=register"
                                class="sso-btn sso-btn--google"
                            >
                                <DashboardIcon name="google" />
                                {{ $t('ui.auth.continueGoogle') }}
                            </a>

                            <a
                                href="/auth/facebook/redirect"
                                class="sso-btn sso-btn--facebook"
                            >
                                <DashboardIcon name="facebook" />
                                {{ $t('ui.auth.continueFacebook') }}
                            </a>
                        </div>

                        <div class="auth-divider">
                            <span>{{ $t('ui.auth.registerEmailDivider') }}</span>
                        </div>

                        <p v-if="$page.props.flash?.error" class="register-sso-notice" role="alert">
                            <DashboardIcon name="info" />
                            {{ $page.props.flash.error }}
                        </p>

                        <form
                            class="register-form"
                            @submit.prevent="submit"
                        >

                            <!-- ROLE -->
                            <div class="role-selector">

                                <label
                                    :class="[
                                        'role-card',
                                        form.role === 'client'
                                            ? 'active'
                                            : ''
                                    ]"
                                >
                                    <input
                                        v-model="form.role"
                                        type="radio"
                                        value="client"
                                    />

                                    <span class="role-card__icon">
                                        <DashboardIcon name="profile" />
                                    </span>

                                    <span>
                                        <strong>
                                            {{ $t('auth.role_client') }}
                                        </strong>

                                        <small>
                                            {{ $t('ui.auth.seekService') }}
                                        </small>
                                    </span>
                                </label>

                                <label
                                    :class="[
                                        'role-card',
                                        form.role === 'prestataire'
                                            ? 'active'
                                            : ''
                                    ]"
                                >
                                    <input
                                        v-model="form.role"
                                        type="radio"
                                        value="prestataire"
                                    />

                                    <span class="role-card__icon">
                                        <DashboardIcon name="experience" />
                                    </span>

                                    <span>
                                        <strong>
                                            {{ $t('auth.role_provider') }}
                                        </strong>

                                        <small>
                                            {{ $t('ui.auth.offerServices') }}
                                        </small>
                                    </span>
                                </label>

                            </div>

                            <!-- NAMES -->
                            <div class="form-grid">

                                <div class="form-group">
                                    <label for="first_name">
                                        {{ $t('auth.first_name') }}
                                    </label>

                                    <div class="input-wrapper">
                                        <DashboardIcon name="profile" />

                                        <input
                                            id="first_name"
                                            v-model="form.first_name"
                                            type="text"
                                            :placeholder="$t('auth.first_name')"
                                            autocomplete="given-name"
                                            required
                                        />
                                    </div>

                                    <p
                                        v-if="errors.first_name"
                                        class="input-error"
                                    >
                                        {{ errors.first_name?.[0] }}
                                    </p>
                                </div>

                                <div class="form-group">
                                    <label for="last_name">
                                        {{ $t('auth.last_name') }}
                                    </label>

                                    <div class="input-wrapper">
                                        <DashboardIcon name="identity" />

                                        <input
                                            id="last_name"
                                            v-model="form.last_name"
                                            type="text"
                                            :placeholder="$t('auth.last_name')"
                                            autocomplete="family-name"
                                            required
                                        />
                                    </div>

                                    <p
                                        v-if="errors.last_name"
                                        class="input-error"
                                    >
                                        {{ errors.last_name?.[0] }}
                                    </p>
                                </div>

                            </div>

                            <!-- EMAIL -->
                            <div class="form-group">
                                <label for="email">
                                    {{ $t('auth.email') }}
                                </label>

                                <div class="input-wrapper">
                                    <DashboardIcon name="mail" />

                                    <input
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        :placeholder="$t('ui.auth.emailPlaceholder')"
                                        autocomplete="email"
                                    />
                                </div>

                                <p
                                    v-if="errors.email"
                                    class="input-error"
                                >
                                    {{ errors.email?.[0] }}
                                </p>
                            </div>

                            <!-- PHONE -->
                            <div class="form-group">
                                <label for="phone">
                                    {{ $t('auth.phone') }}
                                </label>

                                <div class="input-wrapper">
                                    <DashboardIcon name="phone" />

                                    <input
                                        id="phone"
                                        v-model="form.phone"
                                        type="tel"
                                        :placeholder="$t('auth.phone')"
                                        autocomplete="tel"
                                    />
                                </div>

                                <p
                                    v-if="errors.phone"
                                    class="input-error"
                                >
                                    {{ errors.phone?.[0] }}
                                </p>
                            </div>

                            <!-- PASSWORDS -->
                            <div class="form-grid">

                                <div class="form-group">
                                    <label for="password">
                                        {{ $t('auth.password') }}
                                    </label>

                                    <div class="input-wrapper">
                                        <DashboardIcon name="lock" />

                                        <input
                                            id="password"
                                            v-model="form.password"
                                            :type="showPassword ? 'text' : 'password'"
                                            :placeholder="$t('auth.password')"
                                            autocomplete="new-password"
                                        />

                                        <button
                                            type="button"
                                            class="password-toggle"
                                            @click="showPassword = !showPassword"
                                        >
                                            <DashboardIcon :name="showPassword ? 'eye-off' : 'eye'" />
                                        </button>
                                    </div>

                                    <p
                                        v-if="errors.password"
                                        class="input-error"
                                    >
                                        {{ errors.password?.[0] }}
                                    </p>
                                </div>

                                <div class="form-group">
                                    <label for="password_confirmation">
                                        {{ $t('ui.auth.confirmation') }}
                                    </label>

                                    <div class="input-wrapper">
                                        <DashboardIcon name="shield-lock" />

                                        <input
                                            id="password_confirmation"
                                            v-model="form.password_confirmation"
                                            :type="showPasswordConfirmation ? 'text' : 'password'"
                                            :placeholder="$t('ui.auth.confirmation')"
                                            autocomplete="new-password"
                                        />

                                        <button
                                            type="button"
                                            class="password-toggle"
                                            @click="showPasswordConfirmation = !showPasswordConfirmation"
                                        >
                                            <DashboardIcon :name="showPasswordConfirmation ? 'eye-off' : 'eye'" />
                                        </button>
                                    </div>

                                    <p
                                        v-if="errors.password_confirmation"
                                        class="input-error"
                                    >
                                        {{ errors.password_confirmation?.[0] }}
                                    </p>
                                </div>

                            </div>

                            <!-- GLOBAL ERROR -->
                            <p
                                v-if="errors.general"
                                class="input-error input-error--global"
                            >
                                {{ errors.general }}
                            </p>

                            <!-- SUCCESS -->
                            <p
                                v-if="successMessage"
                                class="success-text"
                            >
                                {{ successMessage }}
                            </p>

                            <!-- SUBMIT -->
                            <button
                                type="submit"
                                class="btn btn-primary register-submit"
                                :disabled="loading"
                            >
                                <span v-if="loading">
                                    {{ $t('ui.auth.creating') }}
                                </span>

                                <span v-else>
                                    {{ googleRegistration ? $t('auth.complete_google_registration') : $t('auth.register_title') }}
                                    <DashboardIcon name="arrow" />
                                </span>
                            </button>

                            <p class="register-footer">
                                {{ $t('ui.auth.alreadyAccount') }}

                                <a href="/login">
                                    {{ $t('auth.have_account') }}
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
import { ref } from 'vue'
import { api } from '@/lib/api'
import { router, useForm } from '@inertiajs/vue3'
import { useToastStore } from '@/stores/toast'
import DashboardIcon from '@/Components/DashboardIcon.vue'
import { useI18n } from 'vue-i18n'

const props = defineProps({
    googleProfile: { type: Object, default: null },
})

const toast = useToastStore()
const { t } = useI18n()
const googleRegistration = Boolean(props.googleProfile?.email)

const form = useForm({
    first_name: props.googleProfile?.first_name ?? '',
    last_name: props.googleProfile?.last_name ?? '',
    email: props.googleProfile?.email ?? '',
    phone: '',
    password: '',
    password_confirmation: '',
    role: 'client',
})

const errors = ref({})
const loading = ref(false)
const successMessage = ref(null)

const submit = async () => {
    errors.value = {}
    successMessage.value = null

    // Vérification du mot de passe
    if (!googleRegistration && (!form.password || !form.password_confirmation)) {
        errors.value.password = [t('ui.auth.passwordRequired')]
        errors.value.password_confirmation = [t('ui.auth.confirmationRequired')]

        toast.show(t('ui.auth.bothPasswordsRequired'), 'error')
        return
    } else if (!googleRegistration && form.password !== form.password_confirmation) {
        errors.value.password_confirmation = [
            t('ui.auth.passwordMismatch')
        ]

        toast.show(t('ui.auth.passwordMismatch'), 'error')
        return
    }

    loading.value = true

    try {
        const response = await api.post('/register', {
            first_name: form.first_name,
            last_name: form.last_name,
            email: form.email,
            phone: form.phone,
            password: form.password,
            password_confirmation: form.password_confirmation,
            role: form.role,
        })

        if (response.data.success) {
            successMessage.value = response.data.message || t('ui.auth.registrationSuccess')

            toast.show(successMessage.value, 'success')
            form.reset()
            form.role = 'client'
            errors.value = {}

            if (response.data.redirect) {
                router.visit(response.data.redirect)
            }

            // router.visit('/email/verify', {
            //     data: {
            //         success: successMessage.value,
            //     },
            // })

            // return
        } else {
            errors.value.general = response.data.message || t('ui.auth.registrationError')
        toast.show(errors.value.general, 'error')
        }



    } catch (err) {
        if (err.response?.data?.errors) {
            errors.value = err.response.data.errors

            const messages = Object.values(err.response.data.errors).flat()
            toast.show(messages.join('<br>'), 'error')

        } else if (err.response?.data?.message) {
            errors.value.general = err.response.data.message
            toast.show(err.response.data.message, 'error')

        } else {
            errors.value.general = t('ui.auth.genericError')
            toast.show(t('ui.auth.genericError'), 'error')
        }

    } finally {
        loading.value = false
    }
}
</script>
