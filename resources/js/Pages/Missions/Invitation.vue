<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'

const props = defineProps({
    invitation: { type: Object, required: true },
    token: { type: String, required: true },
})
const { t } = useI18n()
const accepting = ref(false)
const error = ref('')

function accept() {
    accepting.value = true
    error.value = ''
    router.post(`/mission-invitations/${props.invitation.id}/accept`, { token: props.token }, {
        onError: errors => { error.value = errors.invitation || errors.mission || errors.token || t('missions.invitation.acceptError') },
        onFinish: () => { accepting.value = false },
    })
}
</script>

<template>
    <Head :title="$t('missions.invitation.pageTitle')" />
    <AppLayout>
        <main class="invitation-page">
            <article class="invitation-card">
                <span class="eyebrow">{{ $t('missions.invitation.eyebrow') }}</span>
                <h1>{{ invitation.mission.title }}</h1>
                <p>{{ $t('missions.invitation.fromClient', { client: `${invitation.client.first_name} ${invitation.client.last_name}` }) }}</p>
                <dl>
                    <div><dt>{{ $t('missions.show.category') }}</dt><dd>{{ invitation.mission.service?.category?.name }}</dd></div>
                    <div><dt>{{ $t('missions.index.startDate') }}</dt><dd>{{ new Date(invitation.mission.date_start).toLocaleString() }}</dd></div>
                    <div><dt>{{ $t('missions.show.location') }}</dt><dd>{{ invitation.mission.address }}</dd></div>
                </dl>
                <p v-if="invitation.status !== 'pending'" class="notice">{{ $t(`missions.invitation.status.${invitation.status}`) }}</p>
                <p v-if="error" class="error" role="alert">{{ error }}</p>
                <button v-if="invitation.status === 'pending'" type="button" :disabled="accepting" @click="accept">
                    {{ accepting ? $t('missions.invitation.accepting') : $t('missions.invitation.accept') }}
                </button>
            </article>
        </main>
    </AppLayout>
</template>

<style scoped>
.invitation-page{max-width:760px;margin:auto;padding:2rem 1rem}.invitation-card{padding:2rem;border:1px solid #d7e8de;border-radius:18px;background:#fff;box-shadow:0 10px 30px rgba(0,0,0,.06)}.eyebrow{color:#177245;font-size:.75rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase}h1{margin:.5rem 0}dl{display:grid;gap:.75rem;margin:1.5rem 0}dl div{padding:.8rem;border-radius:10px;background:#f7faf8}dt{color:#66756d;font-size:.8rem}dd{margin:.2rem 0 0;font-weight:700}button{padding:.9rem 1.2rem;border:0;border-radius:10px;background:#177245;color:#fff;font-weight:800;cursor:pointer}button:disabled{opacity:.6}.error{color:#b42318;font-weight:700}.notice{color:#72510a;font-weight:700}
</style>
