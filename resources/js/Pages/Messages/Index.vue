<script setup>
import { computed, nextTick, onMounted, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import messageService from '@/composables/messageService'

const props = defineProps({
    initialUserId: { type: Number, default: null },
    initialMissionId: { type: Number, default: null },
})

const page = usePage()
const currentUserId = computed(() => page.props?.auth?.user?.id ?? null)
const conversations = ref([])
const active = ref(null)
const messages = ref([])
const body = ref('')
const loading = ref(true)
const loadingMessages = ref(false)
const sending = ref(false)
const error = ref('')
const thread = ref(null)
const conversationClosed = computed(() => active.value?.mission?.status === 'completed')

function conversationKey(conversation) {
    return `${conversation.participant.id}:${conversation.mission?.id ?? 'direct'}`
}

function fullName(user) {
    return `${user.first_name ?? ''} ${user.last_name ?? ''}`.trim()
}

function formatTime(value) {
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(value))
}

async function scrollToBottom() {
    await nextTick()
    if (thread.value) thread.value.scrollTop = thread.value.scrollHeight
}

async function openConversation(conversation) {
    active.value = conversation
    loadingMessages.value = true
    error.value = ''

    try {
        const { data } = await messageService.conversation(
            conversation.participant.id,
            conversation.mission?.id
        )
        messages.value = data.messages
        conversation.unread_count = 0
        await scrollToBottom()
    } catch (requestError) {
        error.value = requestError.response?.data?.message ?? 'Impossible de charger la conversation.'
    } finally {
        loadingMessages.value = false
    }
}

async function send() {
    const text = body.value.trim()
    if (!text || !active.value || sending.value || conversationClosed.value) return

    sending.value = true
    error.value = ''

    try {
        const { data } = await messageService.send({
            receiver_id: active.value.participant.id,
            mission_id: active.value.mission?.id ?? null,
            message: text,
        })
        messages.value.push(data.message)
        active.value.latest_message = data.message
        body.value = ''
        await scrollToBottom()
    } catch (requestError) {
        error.value = Object.values(requestError.response?.data?.errors ?? {}).flat()[0]
            ?? requestError.response?.data?.message
            ?? 'Impossible d’envoyer le message.'
    } finally {
        sending.value = false
    }
}

onMounted(async () => {
    try {
        const { data } = await messageService.conversations()
        conversations.value = data.conversations

        const selected = conversations.value.find(conversation =>
            conversation.participant.id === props.initialUserId
            && (conversation.mission?.id ?? null) === props.initialMissionId
        ) ?? conversations.value[0]

        if (selected) await openConversation(selected)
    } finally {
        loading.value = false
    }
})
</script>

<template>
    <AppLayout title="Messages">
        <section class="messaging-page">
            <aside class="conversation-list">
                <header>
                    <h1>Messages</h1>
                    <p>Vos échanges avec les clients et prestataires</p>
                </header>

                <div v-if="loading" class="empty-state">Chargement…</div>
                <div v-else-if="!conversations.length" class="empty-state">
                    Aucune conversation pour le moment.
                </div>

                <button
                    v-for="conversation in conversations"
                    :key="conversationKey(conversation)"
                    type="button"
                    class="conversation-item"
                    :class="{ active: active && conversationKey(active) === conversationKey(conversation) }"
                    @click="openConversation(conversation)"
                >
                    <span class="avatar">{{ fullName(conversation.participant).charAt(0) }}</span>
                    <span class="conversation-copy">
                        <strong>{{ fullName(conversation.participant) }}</strong>
                        <small v-if="conversation.mission">{{ conversation.mission.title }}</small>
                        <span>{{ conversation.latest_message.message }}</span>
                    </span>
                    <span v-if="conversation.unread_count" class="unread">
                        {{ conversation.unread_count }}
                    </span>
                </button>
            </aside>

            <main class="chat-panel">
                <template v-if="active">
                    <header class="chat-header">
                        <div>
                            <h2>{{ fullName(active.participant) }}</h2>
                            <p v-if="active.mission">Mission : {{ active.mission.title }}</p>
                            <p v-else>Conversation directe</p>
                        </div>
                    </header>

                    <div ref="thread" class="message-thread">
                        <p v-if="loadingMessages" class="empty-state">Chargement des messages…</p>
                        <div
                            v-for="item in messages"
                            :key="item.id"
                            class="message-row"
                            :class="{ mine: item.sender_id === currentUserId }"
                        >
                            <div class="message-bubble">
                                <p>{{ item.message }}</p>
                                <time>{{ formatTime(item.created_at) }}</time>
                            </div>
                        </div>
                    </div>

                    <p v-if="conversationClosed" class="conversation-closed" role="status">
                        {{ $t('messages.mission_completed') }}
                    </p>
                    <form v-else class="message-composer" @submit.prevent="send">
                        <textarea
                            v-model="body"
                            rows="2"
                            maxlength="5000"
                            placeholder="Écrire un message…"
                            @keydown.enter.exact.prevent="send"
                        />
                        <button type="submit" :disabled="!body.trim() || sending">
                            {{ sending ? 'Envoi…' : 'Envoyer' }}
                        </button>
                    </form>
                    <p v-if="error" class="message-error">{{ error }}</p>
                </template>

                <div v-else class="empty-state chat-empty">
                    Sélectionnez une conversation pour commencer.
                </div>
            </main>
        </section>
    </AppLayout>
</template>

<style scoped lang="scss" src="../../../scss/pages/messages/_index.scss"></style>
