<template>
    <div class="verify-wrapper">
        <div class="verify-card">
            <h1>📧 {{ $t('auth.verify_email_title') }}</h1>

            <p>
                {{ $t('auth.verify_email_text') }}
            </p>

            <button class="btn-primary" :disabled="loading || cooldown > 0" @click="resend">
                <span v-if="cooldown === 0">{{ $t('auth.resend_email') }}</span>
                <span v-else>{{ formatTime(cooldown) }} · {{ $t('auth.resend_email') }}</span>
            </button>

            <p v-if="message" class="success">{{ message }}</p>
            <p v-if="error" class="error">{{ error }}</p>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'
import { useI18n } from 'vue-i18n'

const loading = ref(false)
const message = ref(null)
const error = ref(null)
const { t } = useI18n()

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
        error.value = t('ui.auth.serverError')
    } finally {
        loading.value = false
    }
}

onBeforeUnmount(() => {
    clearInterval(timer)
})
</script>

<style scoped lang="scss" src="../../../scss/pages/auth/_verify-email.scss"></style>
