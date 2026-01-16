<template>
    <AppLayout title="Connexion">

        <!-- Fil d'ariane -->
        <nav class="breadcrumb">
            <ol>
                <li><a href="/">Accueil</a></li>
                <li>Inscription</li>
            </ol>
        </nav>

        <!-- Login Card -->
        <section class="login-section">
            <div class="container">
                <div class="login-card">

                    <h1 class="login-title">Inscription</h1>
                    <p class="login-subtitle">Inscrivez-vous sur Barasira</p>

                    <form @submit.prevent="submit" class="login-form">
                        <div class="form-group">
                            <select id="role" v-model="form.role">
                                <option value="client">Client</option>
                                <option value="prestataire">Prestataire</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="first_name">Prénom</label>
                            <input type="text" id="first_name" v-model="form.first_name" placeholder="Prénom"
                                :class="{ 'input-error': errors.first_name }" />
                            <p v-if="errors.first_name" class="error-text">{{ errors.first_name }}</p>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Nom</label>
                            <input type="text" id="last_name" v-model="form.last_name" placeholder="Nom"
                                :class="{ 'input-error': errors.last_name }" />
                            <p v-if="errors.last_name" class="error-text">{{ errors.last_name }}</p>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" v-model="form.email" placeholder="votre@email.com"
                                :class="{ 'input-error': errors.email }" />
                            <p v-if="errors.email" class="error-text">{{ errors.email }}</p>
                        </div>

                        <div class="form-group">
                            <label for="phone">Téléphone</label>
                            <input type="phone" id="phone" v-model="form.phone" placeholder="Téléphone"
                                :class="{ 'input-error': errors.phone }" />
                            <p v-if="errors.phone" class="error-text">{{ errors.phone }}</p>
                        </div>

                        <div class="form-group">
                            <label for="password">Mot de passe</label>
                            <input type="password" id="password" v-model="form.password" placeholder="Mot de passe"
                                :class="{ 'input-error': errors.password }" />
                            <p v-if="errors.password" class="error-text">{{ errors.password }}</p>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Mot de passe</label>
                            <input type="password" id="password_confirmation" v-model="form.password_confirmation"
                                placeholder="Confirmation" :class="{ 'input-error': errors.password_confirmation }" />
                            <p v-if="errors.password" class="error-text">{{ errors.password_confirmation }}</p>
                        </div>

                        <button :disabled="loading" class="btn-primary btn-block">
                            {{ loading ? 'Création...' : 'Créer le compte' }}
                        </button>
                        <p v-if="errors.general" class="error-text">
                            {{ errors.general[0] }}
                        </p>
                        <p v-if="successMessage" class="success-text">
                            {{ successMessage.value }}
                        </p>

                        <p class="login-footer">
                            Compte déjà existant ? <a href="/login">Connexion</a>
                        </p>
                    </form>
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
