<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import DashboardIcon from '@/Components/DashboardIcon.vue'
import { computed, ref, onBeforeUnmount, onMounted, watch } from "vue"
import { Link, router, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import missionService from '@/composables/missionService'
import reviewService from '@/composables/reviewService'

// const route = useRoute()
// const mission = ref(null)
const page = usePage()
const loading = ref(true)
const applying = ref(false)
const applicationError = ref('')
const applicationSuccess = ref('')
const acceptingApplication = ref(null)
const applicationSelectionError = ref('')
const applicationForm = ref({
    pricing_type: 'hourly',
    hourly_rate: page.props?.auth?.user?.hourly_rate ?? '',
    proposed_price: '',
    message: '',
})
const reviewForm = ref({ rating: 0, comment: '' })
const reviewing = ref(false)
const reviewError = ref('')
const reviewSuccess = ref('')
const editingReview = ref(false)
const missionImages = ref([])
const missionImagePreviews = ref([])
const missionImageError = ref('')
const savingMissionImages = ref(false)
const selectedProviderId = ref('')
const invitingProvider = ref(false)
const invitationError = ref('')
const unassigningProvider = ref(false)
const unassignError = ref('')
const unassignModalOpen = ref(false)
const unassignForm = ref({ reason: '', details: '' })
const { locale, t } = useI18n()
const props = defineProps({
    mission: {
        type: Object,
        required: true
    },
    providers: { type: Array, default: () => [] },
    pendingInvitation: { type: Object, default: null }
})
const currentMission = ref(props.mission)
const hasApplied = ref((props.mission.applications ?? []).some(
    application => application.worker_id === page.props?.auth?.user?.id
))
const canApply = computed(() => page.props?.auth?.user?.role === 'prestataire'
    && currentMission.value.status === 'pending'
    && !currentMission.value.prestataire_id
    && !hasApplied.value)
const canSelectApplication = computed(() => page.props?.auth?.user?.role === 'client'
    && page.props?.auth?.user?.id === currentMission.value.client_id
    && currentMission.value.status === 'pending'
    && !currentMission.value.prestataire_id)
const pendingApplications = computed(() => (currentMission.value.applications ?? []).filter(
    application => application.status === 'en_attente'
))
const acceptedApplication = computed(() => (currentMission.value.applications ?? []).find(
    application => application.status === 'acceptee'
))
const payableAmount = computed(() => {
    if (!acceptedApplication.value) return Number(currentMission.value.price || 0)

    return acceptedApplication.value.pricing_type === 'hourly'
        ? Number(acceptedApplication.value.hourly_rate || 0) * Number(currentMission.value.billable_hours || 0)
        : Number(acceptedApplication.value.proposed_price || 0)
})
const canValidateAndPay = computed(() => page.props?.auth?.user?.role === 'client'
    && page.props?.auth?.user?.id === currentMission.value.client_id
    && currentMission.value.status === 'in_progress'
    && currentMission.value.prestataire_id
    && payableAmount.value > 0)
const canManageMissionImages = computed(() => page.props?.auth?.user?.role === 'client'
    && page.props?.auth?.user?.id === currentMission.value.client_id
    && currentMission.value.status === 'completed')
const canInviteProvider = computed(() => page.props?.auth?.user?.role === 'client'
    && page.props?.auth?.user?.id === currentMission.value.client_id
    && currentMission.value.status === 'pending'
    && !currentMission.value.prestataire_id)
const canUnassignProvider = computed(() => page.props?.auth?.user?.role === 'client'
    && page.props?.auth?.user?.id === currentMission.value.client_id
    && currentMission.value.status === 'in_progress'
    && currentMission.value.prestataire_id)
const assignedProviderName = computed(() => {
    const provider = currentMission.value.prestataire

    return provider
        ? `${provider.first_name} ${provider.last_name}`.trim()
        : t('missions.fields.provider')
})
const currentReview = computed(() => currentMission.value.reviews?.find(
    review => review.reviewer_id === page.props?.auth?.user?.id
) ?? null)
const canReview = computed(() => ['client', 'admin', 'superadmin'].includes(page.props?.auth?.user?.role)
    && (page.props?.auth?.user?.role !== 'client'
        || page.props?.auth?.user?.id === currentMission.value.client_id)
    && currentMission.value.status === 'completed'
    && currentMission.value.prestataire_id
    && !currentReview.value)
const contactUser = computed(() => {
    if (currentMission.value.status === 'completed') return null

    if (page.props?.auth?.user?.id === currentMission.value.client_id) {
        return currentMission.value.prestataire
    }

    if (page.props?.auth?.user?.id === currentMission.value.prestataire_id) {
        return currentMission.value.client
    }

    return null
})


// Mapping des statuts
const statusStyles = computed(() => ({
    pending: { label: t('missions.status.pending'), class: "badge orange" },
    in_progress: { label: t('missions.status.in_progress'), class: "badge blue" },
    completed: { label: t('missions.status.completed'), class: "badge green" },
    cancelled: { label: t('missions.status.cancelled'), class: "badge red" }
}))

function formatDate(dateStr) {
    const date = new Date(dateStr)
    return date.toLocaleDateString(locale.value, {
        day: "2-digit",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit"
    })
}

function chooseMissionImages(event) {
    missionImagePreviews.value.forEach(URL.revokeObjectURL)
    const selected = Array.from(event.target.files ?? [])
    missionImageError.value = ''

    if (selected.length < 1 || selected.length > 5) {
        missionImages.value = []
        missionImagePreviews.value = []
        missionImageError.value = t('missions.show.imagesCountError')
        event.target.value = ''
        return
    }

    missionImages.value = selected
    missionImagePreviews.value = selected.map(URL.createObjectURL)
}

function saveMissionImages() {
    if (!missionImages.value.length) {
        missionImageError.value = t('missions.show.imagesCountError')
        return
    }

    savingMissionImages.value = true
    router.post(`/missions/${currentMission.value.id}/images`, { images: missionImages.value }, {
        forceFormData: true,
        preserveScroll: true,
        onError: errors => {
            missionImageError.value = errors.images || errors['images.0'] || t('missions.show.imagesSaveError')
        },
        onSuccess: () => {
            missionImagePreviews.value.forEach(URL.revokeObjectURL)
            missionImages.value = []
            missionImagePreviews.value = []
            missionImageError.value = ''
        },
        onFinish: () => { savingMissionImages.value = false },
    })
}

function inviteProvider() {
    if (!selectedProviderId.value) {
        invitationError.value = t('missions.show.selectProviderError')
        return
    }

    invitingProvider.value = true
    invitationError.value = ''
    router.post(`/missions/${currentMission.value.id}/invite-provider`, {
        provider_id: selectedProviderId.value,
    }, {
        preserveScroll: true,
        onError: errors => { invitationError.value = errors.provider_id || errors.mission || t('missions.show.invitationError') },
        onFinish: () => { invitingProvider.value = false },
    })
}

function openUnassignModal() {
    unassignForm.value = { reason: '', details: '' }
    unassignError.value = ''
    unassignModalOpen.value = true
}

function closeUnassignModal() {
    if (unassigningProvider.value) return
    unassignModalOpen.value = false
}

function handleUnassignModalKeydown(event) {
    if (event.key === 'Escape' && unassignModalOpen.value) closeUnassignModal()
}

function unassignProvider() {
    if (!unassignForm.value.reason) {
        unassignError.value = t('missions.unassignmentForm.reasonRequired')
        return
    }

    if (unassignForm.value.reason === 'other' && unassignForm.value.details.trim().length < 10) {
        unassignError.value = t('missions.unassignmentForm.detailsRequired')
        return
    }

    unassigningProvider.value = true
    unassignError.value = ''
    router.delete(`/missions/${currentMission.value.id}/provider`, {
        data: {
            reason: unassignForm.value.reason,
            details: unassignForm.value.reason === 'other' ? unassignForm.value.details.trim() : null,
        },
        preserveScroll: true,
        onError: errors => {
            unassignError.value = errors.reason || errors.details || errors.mission || t('missions.show.unassignError')
        },
        onSuccess: () => { unassignModalOpen.value = false },
        onFinish: () => { unassigningProvider.value = false },
    })
}

async function applyToMission() {
    applying.value = true
    applicationError.value = ''
    applicationSuccess.value = ''

    try {
        await missionService.apply(currentMission.value.id, applicationForm.value)
        hasApplied.value = true
        applicationSuccess.value = t('missions.messages.application_success')
    } catch (error) {
        applicationError.value = Object.values(error.response?.data?.errors ?? {}).flat()[0]
            ?? error.response?.data?.message
            ?? t('missions.messages.application_failed')
    } finally {
        applying.value = false
    }
}

async function acceptApplication(application) {
    acceptingApplication.value = application.id
    applicationSelectionError.value = ''

    try {
        const { data } = await missionService.acceptApplication(currentMission.value.id, application.id)
        currentMission.value = data.data
    } catch (error) {
        applicationSelectionError.value = Object.values(error.response?.data?.errors ?? {}).flat()[0]
            ?? error.response?.data?.message
            ?? t('missions.messages.application_accept_failed')
    } finally {
        acceptingApplication.value = null
    }
}

async function submitReview() {
    reviewError.value = ''
    reviewSuccess.value = ''

    if (!reviewForm.value.rating) {
        reviewError.value = t('reviews.ratingRequired')
        return
    }

    reviewing.value = true
    try {
        const payload = {
            rating: reviewForm.value.rating,
            comment: reviewForm.value.comment.trim() || null,
        }
        const { data } = editingReview.value
            ? await reviewService.update(currentReview.value.id, payload)
            : await reviewService.create({ mission_id: currentMission.value.id, ...payload })

        currentMission.value.reviews = editingReview.value
            ? currentMission.value.reviews.map(review => review.id === data.review.id ? data.review : review)
            : [...(currentMission.value.reviews ?? []), data.review]
        reviewSuccess.value = editingReview.value ? t('reviews.updated') : t('reviews.published')
        editingReview.value = false
    } catch (error) {
        reviewError.value = Object.values(error.response?.data?.errors ?? {}).flat()[0]
            ?? error.response?.data?.message
            ?? t('reviews.publishError')
    } finally {
        reviewing.value = false
    }
}

function editReview() {
    reviewForm.value = {
        rating: currentReview.value.rating,
        comment: currentReview.value.comment ?? '',
    }
    reviewError.value = ''
    reviewSuccess.value = ''
    editingReview.value = true
}

function cancelReviewEdit() {
    editingReview.value = false
    reviewError.value = ''
}

async function loadMission() {
    loading.value = true
    // const response = await missionService.show(route.params.id)
    // console.log(response);

    // mission.value = mission
    loading.value = false
}

onMounted(() => {
    loadMission()
    document.addEventListener('keydown', handleUnassignModalKeydown)
})
watch(() => props.mission, mission => { currentMission.value = mission })
watch(unassignModalOpen, open => document.body.classList.toggle('unassign-modal-open', open))
onBeforeUnmount(() => {
    missionImagePreviews.value.forEach(URL.revokeObjectURL)
    document.body.classList.remove('unassign-modal-open')
    document.removeEventListener('keydown', handleUnassignModalKeydown)
})
</script>

<template>
    <AppLayout>
        <div class="mission-show">

            <!-- Skeleton Loader -->
            <div v-if="loading" class="skeleton-wrapper">
                <div class="skeleton-title"></div>
                <div class="skeleton-block"></div>
                <div class="skeleton-block"></div>
                <div class="skeleton-block"></div>
            </div>

            <!-- Mission -->
            <div v-else class="mission-card">

                <!-- Titre + Statut -->
                <div class="header">
                    <h1>{{ currentMission.title }}</h1>
                    <span :class="statusStyles[currentMission.status].class">
                        {{ statusStyles[currentMission.status].label }}
                    </span>
                </div>

                <!-- Infos principales -->
                <div class="info-grid">

                    <div class="info-item">
                        <label>{{ $t('missions.index.startDate') }}</label>
                        <p>{{ formatDate(currentMission.date_start) }}</p>
                    </div>

                    <div class="info-item">
                        <label>{{ $t('missions.index.endDate') }}</label>
                        <p>{{ currentMission.date_end ? formatDate(currentMission.date_end) : $t('missions.show.notSpecified') }}</p>
                    </div>

                    <div class="info-item">
                        <label>{{ $t('missions.show.price') }}</label>
                        <p>{{ currentMission.price }} FCFA</p>
                    </div>

                    <div class="info-item">
                        <label>{{ $t('missions.show.hours') }}</label>
                        <p>{{ currentMission.billable_hours }} h</p>
                    </div>

                    <div class="info-item">
                        <label>{{ $t('missions.fields.provider') }}</label>
                        <p>{{ currentMission.prestataire ? `${currentMission.prestataire.first_name} ${currentMission.prestataire.last_name}` : $t('missions.index.unassigned') }}</p>
                    </div>

                    <div v-if="currentMission.client" class="info-item mission-client-info">
                        <label>{{ $t('missions.fields.client') }}</label>
                        <Link
                            v-if="['prestataire', 'admin', 'superadmin'].includes(page.props?.auth?.user?.role)"
                            :href="`/clients/${currentMission.client.slug}/profile?mission=${encodeURIComponent(currentMission.slug)}`"
                            class="mission-client-link"
                        >
                            <img v-if="currentMission.client.avatar_url" :src="currentMission.client.avatar_url" :alt="`${currentMission.client.first_name} ${currentMission.client.last_name}`">
                            <span v-else>{{ currentMission.client.first_name?.charAt(0) }}{{ currentMission.client.last_name?.charAt(0) }}</span>
                            <strong>{{ currentMission.client.first_name }} {{ currentMission.client.last_name }}</strong>
                            <small>{{ $t('clientProfile.viewProfile') }}</small>
                        </Link>
                        <p v-else>{{ currentMission.client.first_name }} {{ currentMission.client.last_name }}</p>
                    </div>

                    <div class="info-item">
                        <label>{{ $t('missions.show.category') }}</label>
                        <p>{{ currentMission.service?.category?.name  }}</p>
                    </div>

                    <div class="info-item">
                        <label>{{ $t('missions.show.location') }}</label>
                        <p>{{ currentMission.address }}</p>
                    </div>

                </div>

                <!-- Description -->
                <div class="description">
                    <h2>{{ $t('missions.fields.description') }}</h2>
                    <p>{{ currentMission.description }}</p>
                </div>

                <div v-if="canApply || hasApplied || applicationSuccess || applicationError" class="claim-panel">
                    <div>
                        <h2>{{ $t('missions.show.applyTitle') }}</h2>
                        <p>{{ hasApplied ? $t('missions.show.alreadyApplied') : $t('missions.show.applyHint') }}</p>
                    </div>
                    <div v-if="canApply" class="application-pricing-form">
                        <label>
                            <input v-model="applicationForm.pricing_type" type="radio" value="hourly">
                            {{ $t('applications.pricing.hourly') }}
                        </label>
                        <label>
                            <input v-model="applicationForm.pricing_type" type="radio" value="global">
                            {{ $t('applications.pricing.global') }}
                        </label>
                        <label class="application-pricing-form__amount">
                            <span>{{ applicationForm.pricing_type === 'hourly' ? $t('applications.pricing.hourlyAmount') : $t('applications.pricing.globalAmount') }}</span>
                            <input
                                v-if="applicationForm.pricing_type === 'hourly'"
                                v-model.number="applicationForm.hourly_rate"
                                type="number"
                                min="1"
                                step="500"
                                required
                            >
                            <input
                                v-else
                                v-model.number="applicationForm.proposed_price"
                                type="number"
                                min="1"
                                step="500"
                                required
                            >
                        </label>
                        <label class="application-pricing-form__message">
                            <span>{{ $t('applications.proposal_message') }}</span>
                            <textarea v-model.trim="applicationForm.message" rows="3" maxlength="2000"></textarea>
                        </label>
                    </div>
                    <button v-if="canApply" type="button" :disabled="applying" @click="applyToMission">
                        {{ applying ? $t('missions.actions.applying') : $t('missions.actions.apply') }}
                    </button>
                    <p v-if="applicationSuccess" class="claim-feedback success" role="status">{{ applicationSuccess }}</p>
                    <p v-if="applicationError" class="claim-feedback error" role="alert">{{ applicationError }}</p>
                </div>

                <section v-if="canSelectApplication" class="mission-applications-panel">
                    <header>
                        <span>{{ $t('applications.title') }}</span>
                        <h2>{{ $t('applications.received', { count: pendingApplications.length }) }}</h2>
                        <p>{{ $t('applications.selectHint') }}</p>
                    </header>
                    <div v-if="pendingApplications.length" class="mission-applications-list">
                        <article v-for="application in pendingApplications" :key="application.id" class="mission-application">
                            <div class="mission-application__profile">
                                <img v-if="application.user?.avatar_url" :src="application.user.avatar_url" :alt="`${application.user.first_name} ${application.user.last_name}`">
                                <span v-else>{{ application.user?.first_name?.charAt(0) }}{{ application.user?.last_name?.charAt(0) }}</span>
                                <div>
                                    <strong>{{ application.user?.first_name }} {{ application.user?.last_name }}</strong>
                                    <small>{{ Number(application.user?.rating || 0).toFixed(1) }}/5</small>
                                    <small>{{ $t('profile.hourlyRateValue', { amount: Number(application.user?.hourly_rate || 0).toLocaleString(locale) }) }}</small>
                                    <p v-if="application.user?.bio">{{ application.user.bio }}</p>
                                </div>
                            </div>
                            <p v-if="application.message" class="mission-application__message">{{ application.message }}</p>
                            <b v-if="application.pricing_type === 'hourly'">{{ $t('applications.pricing.hourlyValue', { amount: Number(application.hourly_rate).toLocaleString(locale) }) }}</b>
                            <b v-else>{{ $t('applications.pricing.globalValue', { amount: Number(application.proposed_price).toLocaleString(locale) }) }}</b>
                            <button type="button" :disabled="acceptingApplication === application.id" @click="acceptApplication(application)">
                                {{ acceptingApplication === application.id ? $t('applications.actions.accepting') : $t('applications.actions.accept') }}
                            </button>
                        </article>
                    </div>
                    <p v-else>{{ $t('applications.empty') }}</p>
                    <p v-if="applicationSelectionError" class="claim-feedback error" role="alert">{{ applicationSelectionError }}</p>
                </section>

                <section v-if="canInviteProvider" class="provider-invitation-panel">
                    <header>
                        <span>{{ $t('missions.show.providersEyebrow') }}</span>
                        <h2>{{ $t('missions.show.assignProviderTitle') }}</h2>
                        <p>{{ $t('missions.show.assignProviderHint') }}</p>
                    </header>
                    <p v-if="pendingInvitation" class="pending-invitation">
                        {{ $t('missions.show.pendingInvitation', { provider: `${pendingInvitation.provider.first_name} ${pendingInvitation.provider.last_name}` }) }}
                    </p>
                    <div class="provider-invitation-form">
                        <select v-model="selectedProviderId" :aria-label="$t('missions.show.selectProvider')">
                            <option value="">{{ $t('missions.show.selectProvider') }}</option>
                            <option v-for="provider in providers" :key="provider.id" :value="provider.id">
                                {{ provider.first_name }} {{ provider.last_name }} · {{ Number(provider.rating || 0).toFixed(1) }}/5 · {{ $t('profile.hourlyRateValue', { amount: Number(provider.hourly_rate || 0).toLocaleString(locale) }) }}
                            </option>
                        </select>
                        <button type="button" :disabled="invitingProvider || !providers.length" @click="inviteProvider">
                            {{ invitingProvider ? $t('missions.show.sendingInvitation') : $t('missions.show.sendInvitation') }}
                        </button>
                    </div>
                    <p v-if="!providers.length" class="invitation-feedback">{{ $t('missions.show.noProviders') }}</p>
                    <p v-if="invitationError" class="invitation-feedback error" role="alert">{{ invitationError }}</p>
                </section>

                <section v-if="canUnassignProvider" class="provider-unassignment-panel">
                    <div class="provider-unassignment-panel__icon" aria-hidden="true">
                        <DashboardIcon name="delete" />
                    </div>
                    <div class="provider-unassignment-panel__content">
                        <span>{{ $t('missions.show.unassignEyebrow') }}</span>
                        <h2>{{ $t('missions.show.unassignTitle') }}</h2>
                        <p>{{ $t('missions.show.unassignHint') }}</p>
                    </div>
                    <button class="provider-unassignment-panel__action" type="button" @click="openUnassignModal">
                        <DashboardIcon name="delete" />
                        {{ $t('missions.show.unassignProvider') }}
                    </button>
                    <p v-if="unassignError" class="unassign-error" role="alert">
                        <DashboardIcon name="warning" />
                        {{ unassignError }}
                    </p>
                </section>

                <div v-if="canValidateAndPay" class="payment-panel">
                    <div>
                        <h2>{{ $t('missions.show.validateTitle') }}</h2>
                        <p>{{ $t('missions.show.validateHint') }}</p>
                    </div>
                    <Link :href="`/payments/${currentMission.id}`" class="payment-panel__action">
                        <DashboardIcon name="shield" />
                        {{ $t('missions.actions.validateAndPay') }}
                    </Link>
                </div>

                <section v-if="currentMission.images?.length || canManageMissionImages" class="mission-images-panel">
                    <header>
                        <span>{{ $t('missions.show.imagesEyebrow') }}</span>
                        <h2>{{ canManageMissionImages ? (currentMission.images?.length ? $t('missions.show.editImagesTitle') : $t('missions.show.addImagesTitle')) : $t('missions.show.imagesTitle') }}</h2>
                        <p>{{ canManageMissionImages ? $t('missions.show.imagesHint') : $t('missions.show.imagesReadOnlyHint') }}</p>
                    </header>
                    <div v-if="currentMission.images?.length && !missionImagePreviews.length" class="mission-images-grid">
                        <img v-for="image in currentMission.images" :key="image.id" :src="image.url" :alt="currentMission.title">
                    </div>
                    <div v-if="missionImagePreviews.length" class="mission-images-grid">
                        <img v-for="preview in missionImagePreviews" :key="preview" :src="preview" :alt="currentMission.title">
                    </div>
                    <label v-if="canManageMissionImages" class="mission-images-picker">
                        <input type="file" accept="image/jpeg,image/png,image/webp" multiple @change="chooseMissionImages">
                        <span>{{ currentMission.images?.length ? $t('missions.show.replaceImages') : $t('missions.show.chooseImages') }}</span>
                        <small>{{ $t('missions.show.imagesFormats') }}</small>
                    </label>
                    <p v-if="canManageMissionImages && missionImageError" class="review-feedback error" role="alert">{{ missionImageError }}</p>
                    <button v-if="canManageMissionImages" type="button" class="mission-images-save" :disabled="savingMissionImages || !missionImages.length" @click="saveMissionImages">
                        {{ savingMissionImages ? $t('missions.show.savingImages') : $t('missions.show.saveImages') }}
                    </button>
                </section>

                <section v-if="canReview || currentReview" id="review" class="review-panel">
                    <div class="review-panel__heading">
                        <span>{{ $t('reviews.eyebrow') }}</span>
                        <h2>{{ currentReview ? $t('reviews.yourReview') : $t('reviews.title') }}</h2>
                        <p>{{ currentReview ? $t('reviews.thankYou') : $t('reviews.hint') }}</p>
                    </div>

                    <div v-if="currentReview && !editingReview" class="review-summary">
                        <div class="review-stars" :aria-label="$t('reviews.ratingValue', { rating: currentReview.rating })">
                            <span v-for="star in 5" :key="star" aria-hidden="true" :class="{ muted: star > currentReview.rating }">★</span>
                        </div>
                        <p v-if="currentReview.comment">{{ currentReview.comment }}</p>
                        <button v-if="currentReview.edit_count < 1" type="button" class="review-edit-button" @click="editReview">
                            {{ $t('reviews.edit') }}
                        </button>
                        <p v-else class="review-final-notice">{{ $t('reviews.finalNotice') }}</p>
                    </div>

                    <form v-else-if="canReview || editingReview" class="review-form" @submit.prevent="submitReview">
                        <fieldset>
                            <legend>{{ $t('reviews.ratingLabel') }}</legend>
                            <div class="review-star-picker">
                                <button
                                    v-for="star in 5"
                                    :key="star"
                                    type="button"
                                    :class="{ selected: star <= reviewForm.rating }"
                                    :aria-label="$t('reviews.selectRating', { rating: star })"
                                    @click="reviewForm.rating = star"
                                ><span aria-hidden="true">★</span></button>
                            </div>
                        </fieldset>
                        <label>
                            <span>{{ $t('reviews.commentLabel') }}</span>
                            <textarea v-model.trim="reviewForm.comment" rows="4" maxlength="2000" :placeholder="$t('reviews.commentPlaceholder')"></textarea>
                            <small>{{ reviewForm.comment.length }}/2000</small>
                        </label>
                        <p v-if="reviewError" class="review-feedback error" role="alert">{{ reviewError }}</p>
                        <p v-if="reviewSuccess" class="review-feedback success" role="status">{{ reviewSuccess }}</p>
                        <div class="review-form__actions">
                            <button v-if="editingReview" type="button" class="secondary" @click="cancelReviewEdit">{{ $t('reviews.cancelEdit') }}</button>
                            <button type="submit" :disabled="reviewing || !reviewForm.rating">
                                {{ reviewing ? $t('reviews.publishing') : editingReview ? $t('reviews.saveEdit') : $t('reviews.publish') }}
                            </button>
                        </div>
                    </form>
                </section>

                <Link
                    v-if="contactUser"
                    :href="`/messages/create?user=${contactUser.id}&mission=${currentMission.id}`"
                    class="message-link"
                >
                    {{ contactUser.role === 'prestataire' ? $t('missions.show.contactProvider') : $t('missions.show.contactClient') }}
                </Link>

                <div class="unassign-modal-host">
                    <Transition name="unassign-modal">
                        <div v-if="unassignModalOpen" class="unassign-modal__backdrop" @mousedown.self="closeUnassignModal">
                            <section class="unassign-modal" role="dialog" aria-modal="true" aria-labelledby="unassign-modal-title">
                                <button type="button" class="unassign-modal__close" :aria-label="$t('confirmDialog.close')" :disabled="unassigningProvider" @click="closeUnassignModal">×</button>
                                <div class="unassign-modal__icon" aria-hidden="true"><DashboardIcon name="delete" /></div>
                                <div class="unassign-modal__heading">
                                    <span>{{ $t('missions.show.unassignEyebrow') }}</span>
                                    <h2 id="unassign-modal-title">{{ $t('missions.show.unassignTitle') }}</h2>
                                    <p>{{ $t('missions.unassignmentForm.hint', { provider: assignedProviderName }) }}</p>
                                </div>

                                <form @submit.prevent="unassignProvider">
                                    <fieldset>
                                        <legend>{{ $t('missions.unassignmentForm.reasonLabel') }}</legend>
                                        <label v-for="reason in ['unavailable', 'poor_communication', 'lateness', 'disagreement', 'client_change', 'other']" :key="reason" class="unassign-reason">
                                            <input v-model="unassignForm.reason" type="radio" name="unassign_reason" :value="reason" @change="unassignError = ''">
                                            <span>{{ $t(`missions.unassignmentForm.reasons.${reason}`) }}</span>
                                        </label>
                                    </fieldset>

                                    <label v-if="unassignForm.reason === 'other'" class="unassign-modal__details">
                                        <span>{{ $t('missions.unassignmentForm.detailsLabel') }}</span>
                                        <textarea v-model="unassignForm.details" rows="4" maxlength="1000" :placeholder="$t('missions.unassignmentForm.detailsPlaceholder')"></textarea>
                                        <small>{{ unassignForm.details.length }}/1000</small>
                                    </label>

                                    <p v-if="unassignError" class="unassign-modal__error" role="alert"><DashboardIcon name="warning" />{{ unassignError }}</p>

                                    <div class="unassign-modal__actions">
                                        <button type="button" class="secondary" :disabled="unassigningProvider" @click="closeUnassignModal">{{ $t('confirmDialog.cancel') }}</button>
                                        <button type="submit" class="danger" :disabled="unassigningProvider || !unassignForm.reason">
                                            <DashboardIcon :name="unassigningProvider ? 'loading' : 'delete'" />
                                            {{ unassigningProvider ? $t('missions.show.unassigning') : $t('missions.show.unassignProvider') }}
                                        </button>
                                    </div>
                                </form>
                            </section>
                        </div>
                    </Transition>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<style scoped lang="scss" src="../../../scss/pages/missions/_show.scss"></style>
