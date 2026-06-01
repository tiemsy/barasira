<template>
    <AppLayout title="Inscription">
        <section class="register-page">

            <div class="register-shell">

                <!-- LEFT VISUAL -->
                <div class="register-visual" :style="{
                backgroundImage: `
                    linear-gradient(135deg, rgba(0,0,0,.86), rgba(0,0,0,.62)),
                    url('${appUrl}/images/auth-bg.png')
                `
                }">
                    <div class="register-visual__content">

                        <span class="register-visual__badge">
                            🚀 Rejoignez Barasira
                        </span>

                        <h2>
                            Créez votre compte et commencez simplement.
                        </h2>

                        <p>
                            Publiez vos services, trouvez des missions ou contactez des prestataires fiables partout au Mali.
                        </p>

                        <div class="register-visual__features">
                            <span>
                                <i class="bi bi-check-circle-fill"></i>
                                Profil vérifié
                            </span>

                            <span>
                                <i class="bi bi-check-circle-fill"></i>
                                Missions et services
                            </span>

                            <span>
                                <i class="bi bi-check-circle-fill"></i>
                                Messagerie intégrée
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
                                Inscrivez-vous sur Barasira
                            </p>
                        </div>

                        <!-- SSO -->
                        <div class="sso-login">
                            <a
                                href="/auth/google/redirect"
                                class="sso-btn sso-btn--google"
                            >
                                <i class="bi bi-google"></i>
                                Continuer avec Google
                            </a>

                            <a
                                href="/auth/facebook/redirect"
                                class="sso-btn sso-btn--facebook"
                            >
                                <i class="bi bi-facebook"></i>
                                Continuer avec Facebook
                            </a>
                        </div>

                        <div class="auth-divider">
                            <span>ou créez un compte avec votre email</span>
                        </div>

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
                                        <i class="bi bi-person"></i>
                                    </span>

                                    <span>
                                        <strong>
                                            {{ $t('auth.role_client') }}
                                        </strong>

                                        <small>
                                            Je cherche un service
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
                                        <i class="bi bi-briefcase"></i>
                                    </span>

                                    <span>
                                        <strong>
                                            {{ $t('auth.role_provider') }}
                                        </strong>

                                        <small>
                                            Je propose mes services
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
                                        <i class="bi bi-person"></i>

                                        <input
                                            id="first_name"
                                            v-model="form.first_name"
                                            type="text"
                                            placeholder="Prénom"
                                            autocomplete="given-name"
                                        />
                                    </div>

                                    <p
                                        v-if="errors.first_name"
                                        class="input-error"
                                    >
                                        {{ errors.first_name }}
                                    </p>
                                </div>

                                <div class="form-group">
                                    <label for="last_name">
                                        {{ $t('auth.last_name') }}
                                    </label>

                                    <div class="input-wrapper">
                                        <i class="bi bi-person-badge"></i>

                                        <input
                                            id="last_name"
                                            v-model="form.last_name"
                                            type="text"
                                            placeholder="Nom"
                                            autocomplete="family-name"
                                        />
                                    </div>

                                    <p
                                        v-if="errors.last_name"
                                        class="input-error"
                                    >
                                        {{ errors.last_name }}
                                    </p>
                                </div>

                            </div>

                            <!-- EMAIL -->
                            <div class="form-group">
                                <label for="email">
                                    {{ $t('auth.email') }}
                                </label>

                                <div class="input-wrapper">
                                    <i class="bi bi-envelope"></i>

                                    <input
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        placeholder="votre@email.com"
                                        autocomplete="email"
                                    />
                                </div>

                                <p
                                    v-if="errors.email"
                                    class="input-error"
                                >
                                    {{ errors.email }}
                                </p>
                            </div>

                            <!-- PHONE -->
                            <div class="form-group">
                                <label for="phone">
                                    {{ $t('auth.phone') }}
                                </label>

                                <div class="input-wrapper">
                                    <i class="bi bi-telephone"></i>

                                    <input
                                        id="phone"
                                        v-model="form.phone"
                                        type="tel"
                                        placeholder="Téléphone"
                                        autocomplete="tel"
                                    />
                                </div>

                                <p
                                    v-if="errors.phone"
                                    class="input-error"
                                >
                                    {{ errors.phone }}
                                </p>
                            </div>

                            <!-- PASSWORDS -->
                            <div class="form-grid">

                                <div class="form-group">
                                    <label for="password">
                                        {{ $t('auth.password') }}
                                    </label>

                                    <div class="input-wrapper">
                                        <i class="bi bi-lock"></i>

                                        <input
                                            id="password"
                                            v-model="form.password"
                                            :type="showPassword ? 'text' : 'password'"
                                            placeholder="Mot de passe"
                                            autocomplete="new-password"
                                        />

                                        <button
                                            type="button"
                                            class="password-toggle"
                                            @click="showPassword = !showPassword"
                                        >
                                            <i
                                                :class="[
                                                    'bi',
                                                    showPassword
                                                        ? 'bi-eye-slash'
                                                        : 'bi-eye'
                                                ]"
                                            ></i>
                                        </button>
                                    </div>

                                    <p
                                        v-if="errors.password"
                                        class="input-error"
                                    >
                                        {{ errors.password }}
                                    </p>
                                </div>

                                <div class="form-group">
                                    <label for="password_confirmation">
                                        Confirmation
                                    </label>

                                    <div class="input-wrapper">
                                        <i class="bi bi-shield-lock"></i>

                                        <input
                                            id="password_confirmation"
                                            v-model="form.password_confirmation"
                                            :type="showPasswordConfirmation ? 'text' : 'password'"
                                            placeholder="Confirmation"
                                            autocomplete="new-password"
                                        />

                                        <button
                                            type="button"
                                            class="password-toggle"
                                            @click="showPasswordConfirmation = !showPasswordConfirmation"
                                        >
                                            <i
                                                :class="[
                                                    'bi',
                                                    showPasswordConfirmation
                                                        ? 'bi-eye-slash'
                                                        : 'bi-eye'
                                                ]"
                                            ></i>
                                        </button>
                                    </div>

                                    <p
                                        v-if="errors.password_confirmation"
                                        class="input-error"
                                    >
                                        {{ errors.password_confirmation }}
                                    </p>
                                </div>

                            </div>

                            <!-- GLOBAL ERROR -->
                            <p
                                v-if="errors.general"
                                class="input-error input-error--global"
                            >
                                {{ errors.general[0] }}
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
                                    Création...
                                </span>

                                <span v-else>
                                    {{ $t('auth.register_title') }}
                                    <i class="bi bi-arrow-right"></i>
                                </span>
                            </button>

                            <p class="register-footer">
                                Vous avez déjà un compte ?

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

const toast = useToastStore()

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
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
    loading.value = true

    try {
        const response = await api.post('/register', form)

        if (response.data.success) {
            // après inscription → page "vérifier email"

            router.push({
                path: '/email/verify',
                query: {
                    success: response.data.message,
                },
            })

            successMessage.value = response.data.message
            toast.show(successMessage.value)

            // ✅ redirection backend
            // router.visit(res.data.redirect)
            window.location.href = response.data.redirect

            // Redirection Inertia vers le dashboard
            //   Inertia.visit('/dashboard', {replace : true})
        }
    } catch (err) {
        if (err.response && err.response.data.errors) {
            errors.value = err.response.data.errors
        } else if (err.response && err.response.data.message) {
            errors.value.general = err.response.data.message
        } else {
            errors.value.general = 'Une erreur est survenue.'
            toast.show('Une erreur est survenue.', 'error')
        }
    } finally {
        loading.value = false
    }
}
</script>
