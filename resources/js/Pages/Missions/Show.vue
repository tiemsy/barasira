<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { computed, ref, onMounted } from "vue"
import { Link, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import missionService from '@/composables/missionService'
import reviewService from '@/composables/reviewService'

// const route = useRoute()
// const mission = ref(null)
const loading = ref(true)
const claiming = ref(false)
const claimError = ref('')
const claimSuccess = ref('')
const reviewForm = ref({ rating: 0, comment: '' })
const reviewing = ref(false)
const reviewError = ref('')
const reviewSuccess = ref('')
const editingReview = ref(false)
const page = usePage()
const { locale, t } = useI18n()
const props = defineProps({
    mission: {
        type: Object,
        required: true
    }
})
const currentMission = ref(props.mission)
const canClaim = computed(() => page.props?.auth?.user?.role === 'prestataire'
    && currentMission.value.status === 'pending'
    && !currentMission.value.prestataire_id)
const canValidateAndPay = computed(() => page.props?.auth?.user?.role === 'client'
    && page.props?.auth?.user?.id === currentMission.value.client_id
    && currentMission.value.status === 'in_progress'
    && currentMission.value.prestataire_id
    && Number(currentMission.value.price) > 0)
const currentReview = computed(() => currentMission.value.reviews?.find(
    review => review.reviewer_id === page.props?.auth?.user?.id
) ?? null)
const canReview = computed(() => page.props?.auth?.user?.role === 'client'
    && page.props?.auth?.user?.id === currentMission.value.client_id
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

async function claimMission() {
    claiming.value = true
    claimError.value = ''
    claimSuccess.value = ''

    try {
        const { data } = await missionService.claim(currentMission.value.id)
        currentMission.value = data.data
        claimSuccess.value = t('missions.messages.claimed_success')
    } catch (error) {
        claimError.value = error.response?.data?.code === 'MISSION_SCHEDULE_CONFLICT'
            ? t('missions.messages.schedule_conflict')
            : (error.response?.data?.message || t('missions.messages.claim_failed'))
    } finally {
        claiming.value = false
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
                        <label>{{ $t('missions.fields.provider') }}</label>
                        <p>{{ currentMission.prestataire ? `${currentMission.prestataire.first_name} ${currentMission.prestataire.last_name}` : $t('missions.index.unassigned') }}</p>
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

                <div v-if="canClaim || claimSuccess || claimError" class="claim-panel">
                    <div>
                        <h2>{{ $t('missions.show.claimTitle') }}</h2>
                        <p>{{ $t('missions.show.claimHint') }}</p>
                    </div>
                    <button v-if="canClaim" type="button" :disabled="claiming" @click="claimMission">
                        {{ claiming ? $t('missions.actions.claiming') : $t('missions.actions.claim') }}
                    </button>
                    <p v-if="claimSuccess" class="claim-feedback success" role="status">{{ claimSuccess }}</p>
                    <p v-if="claimError" class="claim-feedback error" role="alert">{{ claimError }}</p>
                </div>

                <div v-if="canValidateAndPay" class="payment-panel">
                    <div>
                        <h2>{{ $t('missions.show.validateTitle') }}</h2>
                        <p>{{ $t('missions.show.validateHint') }}</p>
                    </div>
                    <Link :href="`/payments/${currentMission.id}`" class="payment-panel__action">
                        <i class="fas fa-lock"></i>
                        {{ $t('missions.actions.validateAndPay') }}
                    </Link>
                </div>

                <section v-if="canReview || currentReview" class="review-panel">
                    <div class="review-panel__heading">
                        <span>{{ $t('reviews.eyebrow') }}</span>
                        <h2>{{ currentReview ? $t('reviews.yourReview') : $t('reviews.title') }}</h2>
                        <p>{{ currentReview ? $t('reviews.thankYou') : $t('reviews.hint') }}</p>
                    </div>

                    <div v-if="currentReview && !editingReview" class="review-summary">
                        <div class="review-stars" :aria-label="$t('reviews.ratingValue', { rating: currentReview.rating })">
                            <i v-for="star in 5" :key="star" class="fas fa-star" :class="{ muted: star > currentReview.rating }"></i>
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
                                ><i class="fas fa-star"></i></button>
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

            </div>
        </div>
    </AppLayout>
</template>

<style scoped lang="scss" src="../../../scss/pages/missions/_show.scss"></style>
