<script setup>
import { computed } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    client: { type: Object, required: true },
    comments: { type: Array, default: () => [] },
    myComment: { type: Object, default: null },
    backMissionUrl: { type: String, required: true },
})

const page = usePage()
const canComment = computed(() => page.props?.auth?.user?.role === 'prestataire')
const fullName = computed(() => `${props.client.first_name} ${props.client.last_name}`.trim())
const initials = computed(() => `${props.client.first_name?.[0] ?? ''}${props.client.last_name?.[0] ?? ''}`.toUpperCase())
const form = useForm({ comment: props.myComment?.comment ?? '' })
const formatDate = value => new Intl.DateTimeFormat(undefined, { dateStyle: 'medium' }).format(new Date(value))

function submitComment() {
    form.post(`/clients/${props.client.slug}/comments`, {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head :title="$t('clientProfile.pageTitle', { name: fullName })" />
    <AppLayout>
        <main class="client-profile-page">
            <section class="client-profile-hero">
                <div class="client-profile-avatar">
                    <img v-if="client.avatar_url" :src="client.avatar_url" :alt="fullName">
                    <span v-else>{{ initials }}</span>
                </div>
                <div>
                    <span>{{ $t('clientProfile.eyebrow') }} · {{ $t('clientProfile.readOnly') }}</span>
                    <h1>{{ fullName }}</h1>
                    <p>{{ client.bio || $t('clientProfile.noBio') }}</p>
                    <strong>{{ $t('clientProfile.missionsCreated', { count: client.missions_created_count ?? 0 }) }}</strong>
                </div>
                <Link :href="backMissionUrl">{{ $t('clientProfile.backToMission') }}</Link>
            </section>

            <section v-if="canComment" class="client-comment-form">
                <header>
                    <h2>{{ myComment ? $t('clientProfile.editComment') : $t('clientProfile.addComment') }}</h2>
                    <p>{{ $t('clientProfile.commentHint') }}</p>
                </header>
                <form @submit.prevent="submitComment">
                    <textarea v-model.trim="form.comment" rows="5" maxlength="2000" :placeholder="$t('clientProfile.commentPlaceholder')" required />
                    <small>{{ form.comment.length }}/2000</small>
                    <p v-if="form.errors.comment" class="error">{{ form.errors.comment }}</p>
                    <button :disabled="form.processing || form.comment.length < 10">
                        {{ form.processing ? $t('clientProfile.saving') : $t('clientProfile.publish') }}
                    </button>
                </form>
            </section>

            <section class="client-comments">
                <header>
                    <span>{{ $t('clientProfile.communityEyebrow') }}</span>
                    <h2>{{ $t('clientProfile.commentsTitle', { count: comments.length }) }}</h2>
                    <p>{{ $t('clientProfile.visibilityNotice') }}</p>
                </header>
                <div v-if="comments.length">
                    <article v-for="comment in comments" :key="comment.id">
                        <div>
                            <strong>{{ comment.commenter.first_name }} {{ comment.commenter.last_name }}</strong>
                            <small>{{ formatDate(comment.updated_at) }}</small>
                        </div>
                        <p>{{ comment.comment }}</p>
                    </article>
                </div>
                <p v-else class="client-comments-empty">{{ $t('clientProfile.noComments') }}</p>
            </section>
        </main>
    </AppLayout>
</template>

<style scoped>
.client-profile-page{width:min(1000px,calc(100% - 2rem));margin:2rem auto 4rem}.client-profile-hero,.client-comment-form,.client-comments{padding:1.5rem;border:1px solid #dce8e1;border-radius:18px;background:#fff;box-shadow:0 10px 30px rgba(20,70,45,.06)}.client-profile-hero{display:flex;align-items:center;gap:1.2rem}.client-profile-avatar{display:grid;width:88px;height:88px;flex:0 0 auto;place-items:center;overflow:hidden;border-radius:50%;background:#e7f4ec;color:#176b45;font-size:1.5rem;font-weight:900}.client-profile-avatar img{width:100%;height:100%;object-fit:cover}.client-profile-hero>div:nth-child(2){flex:1}.client-profile-hero h1{margin:.25rem 0}.client-profile-hero p{margin:0;color:#68776e}.client-profile-hero>a{color:#176b45;font-weight:800}.client-comment-form,.client-comments{margin-top:1rem}.client-comment-form h2,.client-comments h2{margin:.25rem 0}.client-comment-form header p,.client-comments header p{color:#68776e}.client-comment-form form{display:grid;gap:.6rem}.client-comment-form textarea{padding:.9rem;border:1px solid #b7ccbf;border-radius:10px;font:inherit}.client-comment-form button{justify-self:start;padding:.75rem 1rem;border:0;border-radius:9px;background:#177245;color:#fff;font-weight:800}.client-comment-form .error{color:#b42318}.client-comments>div{display:grid;gap:.75rem;margin-top:1rem}.client-comments article{padding:1rem;border:1px solid #e4ece7;border-radius:11px}.client-comments article>div{display:flex;justify-content:space-between;gap:1rem}.client-comments article p{margin:.6rem 0 0;line-height:1.6}.client-comments-empty{color:#718078}@media(max-width:600px){.client-profile-hero{align-items:flex-start;flex-wrap:wrap}.client-profile-hero>div:nth-child(2){min-width:calc(100% - 110px)}}
</style>
