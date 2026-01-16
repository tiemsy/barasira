<template>
    <AppLayout title="Connexion">

        <!-- Fil d'ariane -->
        <nav class="breadcrumb">
            <ol>
                <li><a href="/">Accueil</a></li>
                <li>Connexion</li>
            </ol>
        </nav>

        <!-- Login Card -->
        <section class="login-section">
            <div class="container">
                <div class="login-card">

                    <h1 class="login-title">Se connecter</h1>
                    <p class="login-subtitle">Accédez à votre compte Barasira</p>

                    <form @submit.prevent="submit" class="login-form">

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" v-model="form.email" placeholder="votre@email.com" />
                            <p v-if="errors.email" class="input-error">
                                {{ errors.email[0] }}
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="password">Mot de passe</label>
                            <input type="password" id="password" v-model="form.password" placeholder="Mot de passe" />
                            <p v-if="errors.password" class="input-error">
                                {{ errors.password[0] }}
                            </p>
                        </div>

                        <!-- REMEMBER -->
                        <label>
                            <input type="checkbox" v-model="form.remember" />
                            Se souvenir de moi
                        </label>

                        <!-- ERREUR GLOBALE -->
                        <p v-if="errors.general" class="input-error">
                            {{ errors.general[0] }}
                        </p>

                        <!-- SUBMIT -->
                        <button :disabled="form.processing" :class="['btn-primary']">
                            {{ form.processing ? 'Connexion...' : 'Se connecter' }}
                        </button>

                        <p class="login-footer">
                            Pas encore de compte ? <a href="/register">Inscription</a>
                        </p>
                    </form>
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


const errors = ref({})
const loading = ref(false)

const form = reactive({
    email: '',
    password: '',
    remember: false,
})

const submit = async () => {
  errors.value = {}
  loading.value = true

  try {
    const res = await api.post('/login', form)

    // console.log(res.data);

    window.location.href = res.data.redirect
    // router.push(res.data.redirect)
  } catch (e) {
    if (e.response?.status === 422) {
      errors.value = e.response.data.errors || {}
    //
    } else if(e.response?.status === 403) {
        router.push('/email/verify')
    }
    else {
      errors.value.general = ['Email non valide. Veuillez valider votre boite mail.']
      router.push('/email/verify')
    }
  } finally {
    loading.value = false
  }
}
</script>
