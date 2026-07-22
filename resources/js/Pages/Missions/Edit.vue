<script setup>
import { onMounted, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import MissionFormFields from '@/Components/Missions/MissionFormFields.vue'
import missionService from '@/composables/missionService'
import { api } from '@/lib/api'
import { useToastStore } from '@/stores/toast'
import { useI18n } from 'vue-i18n'
import {
    emptyMissionForm,
    minimumDateTime,
    normalizeMissionPayload,
    toDateTimeInput,
    validateMissionForm,
} from '@/utils/missionForm'

const props = defineProps({
    mission: { type: Object, required: true },
})

const toast = useToastStore()
const { t } = useI18n()
const services = ref([])
const loading = ref(false)
const errors = ref({})
const form = ref({
    ...emptyMissionForm(),
    ...props.mission,
    service_id: String(props.mission.service_id),
    date_start: toDateTimeInput(props.mission.date_start),
    date_end: toDateTimeInput(props.mission.date_end),
    skills: props.mission.skills ?? [],
    questions: props.mission.questions ?? [],
})

async function submit() {
    errors.value = validateMissionForm(form.value, t)
    if (Object.keys(errors.value).length) return

    loading.value = true
    try {
        await missionService.update(props.mission.id, normalizeMissionPayload(form.value))
        toast.show(t('missions.messages.updated_success'))
        router.visit(`/missions/${props.mission.slug}`)
    } catch (error) {
        errors.value = error.response?.data?.errors ?? {
            general: [t('missions.editPage.updateError')],
        }
    } finally {
        loading.value = false
    }
}

onMounted(async () => {
    const { data } = await api.get('/services')
    services.value = Array.isArray(data?.data) ? data.data : data
})
</script>

<template>
    <AppLayout :title="$t('missions.edit')">
        <section class="mission-create-section">
            <div class="mission-create-shell mission-create-shell--compact">
                <header class="mission-create-hero">
                    <div>
                        <span class="eyebrow">{{ $t('missions.editPage.eyebrow') }}</span>
                        <h1>{{ $t('missions.edit') }}</h1>
                        <p>{{ $t('missions.editPage.subtitle') }}</p>
                    </div>
                </header>
                <form class="mission-card mission-form" novalidate @submit.prevent="submit">
                    <MissionFormFields
                        :form="form"
                        :errors="errors"
                        :services="services"
                        :minimum-date="minimumDateTime()"
                    />
                    <p v-if="errors.general || errors.mission" class="alert-error">
                        {{ errors.mission?.[0] ?? errors.general?.[0] }}
                    </p>
                    <footer class="mission-form__actions">
                        <button type="submit" class="btn-submit" :disabled="loading">
                            {{ loading ? $t('missions.editPage.saving') : $t('missions.editPage.save') }}
                        </button>
                    </footer>
                </form>
            </div>
        </section>
    </AppLayout>
</template>
