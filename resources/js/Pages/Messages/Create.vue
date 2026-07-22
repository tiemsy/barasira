<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import messageService from '@/composables/messageService'
import { useI18n } from 'vue-i18n'

const props = defineProps({
    recipient: { type: Object, default: null },
    mission: { type: Object, default: null },
})

const body = ref('')
const loading = ref(false)
const error = ref('')
const { t } = useI18n()
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
            ?? t('ui.messages.sendError')
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <AppLayout :title="$t('ui.messages.newTitle')">
        <section class="new-message">
            <h1>{{ $t('ui.messages.newTitle') }}</h1>
            <template v-if="recipient">
                <p>{{ $t('ui.messages.to') }} : <strong>{{ fullName(recipient) }}</strong></p>
                <p v-if="mission">{{ $t('ui.messages.mission') }} : <strong>{{ mission.title }}</strong></p>
                <p v-if="conversationClosed" class="conversation-closed" role="status">
                    {{ $t('messages.mission_completed') }}
                </p>
                <form v-else @submit.prevent="send">
                    <textarea v-model="body" rows="8" maxlength="5000" :placeholder="$t('ui.messages.placeholder')" />
                    <p v-if="error" class="error">{{ error }}</p>
                    <button type="submit" :disabled="loading || !body.trim()">
                        {{ loading ? $t('ui.messages.sending') : $t('ui.messages.sendMessage') }}
                    </button>
                </form>
            </template>
            <p v-else class="error">{{ $t('ui.messages.recipientMissing') }}</p>
        </section>
    </AppLayout>
</template>

<style scoped lang="scss" src="../../../scss/pages/messages/_create.scss"></style>
