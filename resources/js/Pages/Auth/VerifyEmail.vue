<template>
    <div class="verify-wrapper">
        <div class="verify-card">
            <h1>📧 Vérification requise</h1>

            <p>
                Un lien de confirmation a été envoyé à votre adresse email.
                Veuillez cliquer dessus pour activer votre compte.
            </p>

            <button class="btn-primary" :disabled="loading || cooldown > 0" @click="resend">
                <span v-if="cooldown === 0">Renvoyer l’email</span>
                <span v-else>Le lien expire dans {{ formatTime(cooldown) }}. Renvoyer à nouveau</span>
            </button>

            <p v-if="message" class="success">{{ message }}</p>
            <p v-if="error" class="error">{{ error }}</p>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'

const loading = ref(false)
const message = ref(null)
const error = ref(null)

const cooldown = ref(0) // cooldown global
let timer = null

const formatTime = (value) => {
    const hours = Math.floor(value / 3600)
    const minutes = Math.floor((value % 3600) / 60)
    const seconds = value % 60

    const pad = (n) => String(n).padStart(2, '0')

    return `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`
}

const startCooldown = () => {
    clearInterval(timer)
    timer = setInterval(() => {
        cooldown.value--
        if (cooldown.value <= 0) {
            clearInterval(timer)
        }
    }, 1000)
}

const resend = async () => {
    if (cooldown > 0) return // empêche le spam

    loading.value = true
    error.value = null
    message.value = null

    try {
        const res = await axios.post('/email/resend')
        message.value = res.data.message

        cooldown.value = 3600
        startCooldown()

    } catch (e) {
        error.value = 'Impossible de renvoyer l’email.'
    } finally {
        loading.value = false
    }
}

onBeforeUnmount(() => {
    clearInterval(timer)
})
</script>

<style scoped>
.verify-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    /* background: linear-gradient(135deg, #0d6efd, #20c997); */
}

.verify-card {
    background: #fff;
    padding: 40px;
    border-radius: 14px;
    max-width: 420px;
    text-align: center;
}

.success {
    color: #198754;
    margin-top: 12px;
}

.error {
    color: #dc3545;
    margin-top: 12px;
}
</style>
