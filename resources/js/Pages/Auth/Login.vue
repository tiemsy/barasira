<template>
    <AppLayout title="Connexion">
        <section class="login-page">

            <div class="login-shell">

                <!-- LEFT VISUAL -->
                <div class="login-visual">
                    <div class="login-visual__content">
                        <span class="login-visual__badge">
                            🔐 Barasira sécurisé
                        </span>

                        <h2>
                            Connectez-vous et trouvez le bon prestataire rapidement.
                        </h2>

                        <p>
                            Accédez à vos missions, services, messages et recommandations personnalisées.
                        </p>

                        <div class="login-visual__features">
                            <span>
                                <i class="bi bi-check-circle-fill"></i>
                                Prestataires vérifiés
                            </span>

                            <span>
                                <i class="bi bi-check-circle-fill"></i>
                                Missions sécurisées
                            </span>

                            <span>
                                <i class="bi bi-check-circle-fill"></i>
                                Support rapide
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
                                Accédez à votre compte Barasira
                            </p>
                        </div>

                        <!-- SSO -->
                        <div class="sso-login">
                            <!-- <a href="/api/auth/google/redirect" class="sso-btn sso-btn--google">
                                <i class="bi bi-google"></i>
                                Continuer avec Google
                            </a> -->

                            <button type="button" class="sso-btn sso-btn--google" @click="loginWithGoogle">
                                <i class="fab fa-google"></i>
                                Continuer avec Google
                            </button>

                            <a href="/api/auth/facebook/redirect" class="sso-btn sso-btn--facebook">
                                <i class="bi bi-facebook"></i>
                                Continuer avec Facebook
                            </a>
                        </div>

                        <div class="auth-divider">
                            <span>ou connectez-vous avec votre email</span>
                        </div>

                        <form class="login-form" @submit.prevent="submit">

                            <!-- EMAIL -->
                            <div class="form-group">
                                <label for="email">
                                    {{ $t('auth.email') }}
                                </label>

                                <div class="input-wrapper">
                                    <i class="bi bi-envelope"></i>

                                    <input id="email" v-model="form.email" type="email" placeholder="votre@email.com"
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
                                    <i class="bi bi-lock"></i>

                                    <input id="password" v-model="form.password"
                                        :type="showPassword ? 'text' : 'password'" placeholder="Mot de passe"
                                        autocomplete="current-password" />

                                    <button type="button" class="password-toggle" @click="showPassword = !showPassword">
                                        <i :class="[
                                            'bi',
                                            showPassword
                                                ? 'bi-eye-slash'
                                                : 'bi-eye'
                                        ]"></i>
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
                                    Mot de passe oublié ?
                                </a>
                            </div>

                            <!-- GLOBAL ERROR -->
                            <p v-if="errors.general" class="input-error input-error--global">
                                {{ errors.general[0] }}
                            </p>

                            <!-- SUBMIT -->
                            <button type="submit" class="btn btn-primary login-submit" :disabled="form.processing">
                                <span v-if="form.processing">
                                    Connexion...
                                </span>

                                <span v-else>
                                    {{ $t('navigation.login') }}
                                    <i class="bi bi-arrow-right"></i>
                                </span>
                            </button>

                            <p class="login-footer">
                                Pas encore de compte ?

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

const baseURL = import.meta.env.VITE_API_URL

const errors = ref({})
const loading = ref(false)

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
            errors.value.general = ['Veuillez valider votre boîte mail.']
            router.push('/email/verify')
        }
        else {
            errors.value.general = [
                e.response?.data?.message || 'Erreur serveur. Veuillez réessayer.'
            ]
        }
    } finally {
        loading.value = false
    }
}
</script>
