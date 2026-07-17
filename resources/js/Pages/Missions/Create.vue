<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import MissionFormFields from '@/Components/Missions/MissionFormFields.vue'
import missionService from '@/composables/missionService'
import { api } from '@/lib/api'
import { useToastStore } from '@/stores/toast'
import { useI18n } from 'vue-i18n'
import {
    emptyMissionForm,
    firstValidationError,
    minimumDateTime,
    normalizeMissionPayload,
    validateMissionForm,
} from '@/utils/missionForm'

const DRAFT_KEY = 'barasira:mission-draft'
const toast = useToastStore()
const { locale, t } = useI18n()
const form = ref(emptyMissionForm())
const services = ref([])
const errors = ref({})
const loading = ref(false)
const servicesLoading = ref(true)
const aiKeywords = ref('')
const aiLoading = ref(false)
const aiError = ref('')
const aiGenerated = ref(false)
const draftRestored = ref(false)

const minimumDate = minimumDateTime()
const firstError = field => firstValidationError(errors.value, field)
const canGenerateMission = computed(() => aiKeywords.value.trim().length >= 3 && !aiLoading.value)
const requiredFields = ['title', 'service_id', 'description', 'city', 'address', 'date_start']
const completedRequiredFields = computed(() =>
    requiredFields.filter(field => String(form.value[field] ?? '').trim()).length
)
const progress = computed(() =>
    Math.round((completedRequiredFields.value / requiredFields.length) * 100)
)
const selectedService = computed(() =>
    services.value.find(service => String(service.id) === String(form.value.service_id))
)

function restoreDraft() {
    try {
        const saved = JSON.parse(localStorage.getItem(DRAFT_KEY))
        if (!saved) return
        form.value = { ...emptyMissionForm(), ...saved }
        draftRestored.value = true
    } catch {
        localStorage.removeItem(DRAFT_KEY)
    }
}

function applyAiMission(mission) {
    const generated = {
        title: mission.title ?? '',
        description: mission.description ?? '',
        service_id: mission.service_id != null ? String(mission.service_id) : '',
        city: mission.city ?? '',
        address: mission.address ?? '',
        price: mission.price ?? '',
        skills: Array.isArray(mission.skills) ? mission.skills : [],
        questions: Array.isArray(mission.questions) ? mission.questions : [],
    }

    form.value = {
        ...form.value,
        ...generated,
        date_start: form.value.date_start,
        date_end: form.value.date_end,
    }
}

async function generateMissionWithAi() {
    if (!canGenerateMission.value) return
    aiLoading.value = true
    aiError.value = ''
    aiGenerated.value = false

    try {
        const { data } = await api.post('/missions/generate-with-ai', {
            keywords: aiKeywords.value.trim(),
        })
        if (!data?.mission) throw new Error(t('missions.createPage.aiMissingResponse'))
        applyAiMission(data.mission)
        aiGenerated.value = true
        toast.show(t('missions.createPage.aiApplied'))
    } catch (error) {
        const response = error.response
        aiError.value = response?.status === 429
            ? t('missions.createPage.aiLimit')
            : t('missions.createPage.aiError')
    } finally {
        aiLoading.value = false
    }
}

async function fetchServices() {
    servicesLoading.value = true
    try {
        const { data } = await api.get('/services')
        services.value = Array.isArray(data?.data) ? data.data : (Array.isArray(data) ? data : [])
    } catch (error) {
        errors.value.general = [
            error.response?.data?.message ?? t('missions.createPage.servicesError'),
        ]
    } finally {
        servicesLoading.value = false
    }
}

async function submit() {
    if (loading.value) return
    errors.value = validateMissionForm(form.value, t)

    if (Object.keys(errors.value).length) {
        toast.show(t('missions.createPage.checkRequired'), 'error')
        document.querySelector('.invalid')?.focus()
        return
    }

    loading.value = true
    try {
        const { data } = await missionService.create(normalizeMissionPayload(form.value))
        localStorage.removeItem(DRAFT_KEY)
        toast.show(t('missions.messages.created_success'))
        router.visit('/missions/index')
    } catch (error) {
        const response = error.response
        errors.value = response?.status === 422
            ? response.data?.errors ?? {}
            : {
                general: [
                    response?.status === 401
                        ? t('missions.createPage.authenticationRequired')
                        : response?.status === 403
                            ? t('missions.createPage.emailVerificationRequired')
                            : t('missions.createPage.createError'),
                ],
            }
        toast.show(t('missions.createPage.publishError'), 'error')
    } finally {
        loading.value = false
    }
}

watch(form, value => {
    localStorage.setItem(DRAFT_KEY, JSON.stringify(value))
}, { deep: true })

onMounted(() => {
    restoreDraft()
    fetchServices()
})
</script>

<template>
    <AppLayout :title="$t('missions.create')">
        <section class="mission-create-section">
            <div class="mission-create-shell">
                <header class="mission-create-hero">
                    <div>
                        <span class="eyebrow">{{ $t('missions.createPage.eyebrow') }}</span>
                        <h1>{{ $t('missions.createPage.title') }}</h1>
                        <p>{{ $t('missions.createPage.subtitle') }}</p>
                    </div>
                    <div class="form-progress" :aria-label="$t('missions.createPage.progressLabel')">
                        <strong>{{ progress }}%</strong>
                        <progress :value="progress" max="100">{{ progress }}%</progress>
                        <small>{{ $t('missions.createPage.requiredProgress', { completed: completedRequiredFields, total: requiredFields.length }) }}</small>
                    </div>
                </header>

                <p v-if="draftRestored" class="draft-notice">
                    {{ $t('missions.createPage.draftRestored') }}
                </p>

                <section class="mission-ai-generator">
                    <div class="mission-ai-generator__header">
                        <div>
                            <span class="mission-ai-generator__badge">{{ $t('missions.createPage.aiBadge') }}</span>
                            <h2>{{ $t('missions.createPage.aiTitle') }}</h2>
                            <p>{{ $t('missions.createPage.aiDescription') }}</p>
                        </div>
                    </div>
                    <div class="mission-ai-generator__content">
                        <label for="ai-keywords">{{ $t('missions.createPage.aiLabel') }}</label>
                        <textarea id="ai-keywords" v-model="aiKeywords" rows="4" maxlength="1000"
                            :placeholder="$t('missions.createPage.aiPlaceholder')"
                            @keydown.ctrl.enter.prevent="generateMissionWithAi"
                            @keydown.meta.enter.prevent="generateMissionWithAi" />
                        <div class="mission-ai-generator__footer">
                            <small>{{ aiKeywords.length }}/1000 · Ctrl/⌘ + Entrée</small>
                            <button type="button" class="btn-generate" :disabled="!canGenerateMission"
                                @click="generateMissionWithAi">
                                <span v-if="aiLoading" class="spinner" aria-hidden="true" />
                                {{ aiLoading ? $t('missions.createPage.generating') : aiGenerated ? $t('missions.createPage.regenerate') : $t('missions.createPage.prefill') }}
                            </button>
                        </div>
                        <p v-if="aiError" class="mission-ai-generator__error">{{ aiError }}</p>
                    </div>
                </section>

                <div class="mission-create-layout">
                    <form class="mission-card mission-form" novalidate @submit.prevent="submit">
                        <MissionFormFields
                            :form="form"
                            :errors="errors"
                            :services="services"
                            :minimum-date="minimumDate"
                            translation-enabled
                        />

                        <div v-if="errors.general" class="alert-error">{{ firstError('general') }}</div>

                        <footer class="mission-form__actions">
                            <p>{{ $t('missions.createPage.statusNotice') }}</p>
                            <button type="submit" class="btn-submit" :disabled="loading || servicesLoading">
                                {{ loading ? $t('missions.createPage.publishing') : $t('missions.createPage.publish') }}
                            </button>
                        </footer>
                    </form>

                    <aside class="mission-summary">
                        <span class="eyebrow">{{ $t('missions.createPage.preview') }}</span>
                        <h2>{{ form.title || $t('missions.createPage.previewTitle') }}</h2>
                        <p>{{ form.description || $t('missions.createPage.previewDescription') }}</p>
                        <dl>
                            <div><dt>{{ $t('missions.fields.service') }}</dt><dd>{{ selectedService?.name || $t('missions.createPage.selectValue') }}</dd></div>
                            <div><dt>{{ $t('missions.createPage.location') }}</dt><dd>{{ [form.city, form.address].filter(Boolean).join(', ') || $t('missions.createPage.specifyValue') }}</dd></div>
                            <div><dt>{{ $t('missions.createPage.budget') }}</dt><dd>{{ form.price ? `${Number(form.price).toLocaleString(locale)} FCFA` : $t('missions.createPage.negotiable') }}</dd></div>
                            <div><dt>{{ $t('missions.index.startDate') }}</dt><dd>{{ form.date_start ? new Date(form.date_start).toLocaleString(locale) : $t('missions.createPage.specifyValue') }}</dd></div>
                        </dl>
                    </aside>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
