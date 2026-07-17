<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import messageService from '@/composables/messageService'

const props = defineProps({
    recipient: { type: Object, default: null },
    mission: { type: Object, default: null },
})

const body = ref('')
const loading = ref(false)
const error = ref('')
const conversationClosed = computed(() => props.mission?.status === 'completed')

const fullName = user => `${user?.first_name ?? ''} ${user?.last_name ?? ''}`.trim()

async function send() {
    if (!props.recipient || !body.value.trim() || conversationClosed.value) return
    loading.value = true
    error.value = ''

    try {
        await messageService.send({
            receiver_id: props.recipient.id,
            mission_id: props.mission?.id ?? null,
            message: body.value.trim(),
        })
        router.visit(`/messages?user=${props.recipient.id}${props.mission ? `&mission=${props.mission.id}` : ''}`)
    } catch (requestError) {
        error.value = Object.values(requestError.response?.data?.errors ?? {}).flat()[0]
            ?? requestError.response?.data?.message
            ?? 'Impossible d’envoyer le message.'
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <AppLayout title="Nouveau message">
        <section class="new-message">
            <h1>Nouveau message</h1>
            <template v-if="recipient">
                <p>À : <strong>{{ fullName(recipient) }}</strong></p>
                <p v-if="mission">Mission : <strong>{{ mission.title }}</strong></p>
                <p v-if="conversationClosed" class="conversation-closed" role="status">
                    {{ $t('messages.mission_completed') }}
                </p>
                <form v-else @submit.prevent="send">
                    <textarea v-model="body" rows="8" maxlength="5000" placeholder="Écrivez votre message…" />
                    <p v-if="error" class="error">{{ error }}</p>
                    <button type="submit" :disabled="loading || !body.trim()">
                        {{ loading ? 'Envoi…' : 'Envoyer le message' }}
                    </button>
                </form>
            </template>
            <p v-else class="error">Le destinataire est introuvable.</p>
        </section>
    </AppLayout>
</template>

<style scoped lang="scss" src="../../../scss/pages/messages/_create.scss"></style>
