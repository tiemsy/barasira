<script setup>
import { reactive } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import DashboardIcon from '@/Components/DashboardIcon.vue'

const props = defineProps({
    reviews: { type: Object, required: true },
    myReview: { type: Object, default: null },
    averageRating: { type: Number, default: 0 },
})

const page = usePage()
const form = reactive({ rating: props.myReview?.rating ?? 0, comment: props.myReview?.comment ?? '' })
const state = reactive({ processing: false })

function submit() {
    state.processing = true
    router.post('/avis', form, {
        preserveScroll: true,
        onFinish: () => { state.processing = false },
    })
}
</script>

<template>
    <AppLayout>
        <div class="platform-reviews-page">
            <section class="platform-reviews-hero">
                <span>{{ $t('platformReviews.eyebrow') }}</span>
                <h1>{{ $t('platformReviews.title') }}</h1>
                <p>{{ $t('platformReviews.subtitle') }}</p>
                <strong v-if="reviews.total">★ {{ averageRating }}/5 · {{ $t('platformReviews.count', { count: reviews.total }) }}</strong>
            </section>

            <div class="platform-reviews-layout">
                <section class="platform-review-form-card">
                    <template v-if="page.props.auth?.user">
                        <h2>{{ myReview ? $t('platformReviews.editTitle') : $t('platformReviews.formTitle') }}</h2>
                        <form @submit.prevent="submit">
                            <fieldset>
                                <legend>{{ $t('platformReviews.rating') }}</legend>
                                <div class="platform-review-stars">
                                    <button v-for="star in 5" :key="star" type="button" :class="{ active: star <= form.rating }" :aria-label="$t('platformReviews.star', { star })" @click="form.rating = star">★</button>
                                </div>
                                <small v-if="page.props.errors?.rating">{{ page.props.errors.rating }}</small>
                            </fieldset>
                            <label for="platform-review-comment">{{ $t('platformReviews.comment') }}</label>
                            <textarea id="platform-review-comment" v-model.trim="form.comment" rows="6" minlength="10" maxlength="1500" required :placeholder="$t('platformReviews.placeholder')"></textarea>
                            <small v-if="page.props.errors?.comment">{{ page.props.errors.comment }}</small>
                            <button class="platform-review-submit" :disabled="state.processing || !form.rating || form.comment.length < 10">
                                {{ state.processing ? $t('platformReviews.saving') : $t('platformReviews.publish') }}
                            </button>
                        </form>
                    </template>
                    <template v-else>
                        <DashboardIcon name="rating" />
                        <h2>{{ $t('platformReviews.loginTitle') }}</h2>
                        <p>{{ $t('platformReviews.loginText') }}</p>
                        <Link href="/login" class="platform-review-submit">{{ $t('platformReviews.login') }}</Link>
                    </template>
                </section>

                <section class="platform-reviews-list">
                    <article v-for="review in reviews.data" :key="review.id">
                        <div class="platform-review-author">
                            <img v-if="review.user.avatar_url" :src="review.user.avatar_url" :alt="review.user.first_name">
                            <span v-else>{{ review.user.first_name?.charAt(0) }}{{ review.user.last_name?.charAt(0) }}</span>
                            <div><strong>{{ review.user.first_name }} {{ review.user.last_name?.charAt(0) }}.</strong><small>{{ $t(`platformReviews.roles.${review.user.role}`) }}</small></div>
                        </div>
                        <div class="platform-review-rating" :aria-label="`${review.rating}/5`">{{ '★'.repeat(review.rating) }}<span>{{ '★'.repeat(5 - review.rating) }}</span></div>
                        <p>{{ review.comment }}</p>
                        <time :datetime="review.created_at">{{ new Date(review.created_at).toLocaleDateString() }}</time>
                    </article>
                    <p v-if="!reviews.data.length" class="platform-reviews-empty">{{ $t('platformReviews.empty') }}</p>
                    <nav v-if="reviews.links?.length > 3" class="platform-reviews-pagination" :aria-label="$t('platformReviews.pagination')">
                        <Link v-for="link in reviews.links" :key="link.label" :href="link.url || '#'" :class="{ active: link.active, disabled: !link.url }" preserve-scroll v-html="link.label" />
                    </nav>
                </section>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.platform-reviews-page{background:#f7f8f4;min-height:70vh;padding:3rem 1rem 5rem}.platform-reviews-hero{max-width:900px;margin:0 auto 2.5rem;text-align:center}.platform-reviews-hero>span{color:#227a4b;font-weight:800;text-transform:uppercase;letter-spacing:.1em}.platform-reviews-hero h1{font-size:clamp(2rem,5vw,3.5rem);margin:.5rem 0}.platform-reviews-hero p{color:#58615c;font-size:1.1rem}.platform-reviews-hero strong{display:inline-block;margin-top:.8rem;color:#9a6a00}.platform-reviews-layout{max-width:1180px;margin:auto;display:grid;grid-template-columns:380px 1fr;gap:2rem;align-items:start}.platform-review-form-card,.platform-reviews-list article{background:#fff;border:1px solid #e3e7df;border-radius:18px;box-shadow:0 12px 35px rgba(32,65,47,.06)}.platform-review-form-card{padding:1.5rem;position:sticky;top:1rem}.platform-review-form-card h2{margin-top:0}.platform-review-form-card fieldset{border:0;padding:0;margin:1rem 0}.platform-review-stars{display:flex;gap:.25rem}.platform-review-stars button{border:0;background:none;color:#c8cec9;font-size:2rem;cursor:pointer;padding:0}.platform-review-stars button.active,.platform-review-rating{color:#e1a400}.platform-review-form-card label{display:block;font-weight:700;margin-bottom:.5rem}.platform-review-form-card textarea{width:100%;border:1px solid #ccd4cd;border-radius:10px;padding:.8rem;resize:vertical}.platform-review-form-card small{display:block;color:#b42318;margin-top:.35rem}.platform-review-submit{display:inline-flex;justify-content:center;border:0;border-radius:10px;background:#227a4b;color:#fff;padding:.8rem 1rem;font-weight:700;text-decoration:none;margin-top:1rem;width:100%;cursor:pointer}.platform-review-submit:disabled{opacity:.55}.platform-reviews-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem}.platform-reviews-list article{padding:1.25rem}.platform-review-author{display:flex;align-items:center;gap:.75rem}.platform-review-author>img,.platform-review-author>span{width:42px;height:42px;border-radius:50%;object-fit:cover;background:#e6f2ea;display:grid;place-items:center;color:#227a4b;font-weight:800}.platform-review-author small{display:block;color:#6b746e}.platform-review-rating{margin:.9rem 0}.platform-review-rating span{color:#d9dedb}.platform-reviews-list article p{line-height:1.6;overflow-wrap:anywhere}.platform-reviews-list time{color:#7b837e;font-size:.85rem}.platform-reviews-empty,.platform-reviews-pagination{grid-column:1/-1}.platform-reviews-pagination{display:flex;justify-content:center;gap:.35rem}.platform-reviews-pagination a{padding:.5rem .7rem;border-radius:7px;background:#fff;text-decoration:none;color:#227a4b}.platform-reviews-pagination a.active{background:#227a4b;color:#fff}.platform-reviews-pagination a.disabled{pointer-events:none;opacity:.45}@media(max-width:800px){.platform-reviews-layout{grid-template-columns:1fr}.platform-review-form-card{position:static}.platform-reviews-list{grid-template-columns:1fr}}
</style>
