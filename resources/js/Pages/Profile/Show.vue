<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'
import ProviderCredentials from '@/Components/Profile/ProviderCredentials.vue'
import ProviderDocuments from '@/Components/Profile/ProviderDocuments.vue'
import DashboardIcon from '@/Components/DashboardIcon.vue'

const props = defineProps({
    profile: { type: Object, required: true },
    completedMissions: { type: Array, default: () => [] },
    resume: { type: Object, default: null },
    documents: { type: Array, default: () => [] },
})

const { t } = useI18n()
const fullName = computed(() => `${props.profile.first_name} ${props.profile.last_name}`.trim())
const initials = computed(() => `${props.profile.first_name?.[0] ?? ''}${props.profile.last_name?.[0] ?? ''}`.toUpperCase())
const roleLabel = computed(() => t(`navbar.roles.${props.profile.role === 'prestataire' ? 'provider' : props.profile.role}`))
const formatDate = value => value ? new Intl.DateTimeFormat(undefined, { dateStyle: 'long' }).format(new Date(value)) : ''
</script>

<template>
    <AppLayout :title="$t('profile.title')">
        <main class="profile-page">
            <section class="profile-hero">
                <div class="profile-avatar" :class="{ 'has-image': profile.avatar_url }">
                    <img v-if="profile.avatar_url" :src="profile.avatar_url" :alt="fullName">
                    <span v-else>{{ initials }}</span>
                </div>
                <div class="profile-identity">
                    <span class="profile-eyebrow">{{ $t('profile.account') }}</span>
                    <h1>{{ fullName }}</h1>
                    <p>{{ roleLabel }}</p>
                    <span v-if="profile.role === 'prestataire'" :class="['profile-verified', { 'is-unverified': !profile.identity_verified_at }]"><DashboardIcon :name="profile.identity_verified_at ? 'verified' : 'shield'" />{{ profile.identity_verified_at ? $t('serviceShow.verifiedProfile') : $t('serviceShow.unverifiedProfile', 'Profil non vérifié') }}</span>
                </div>
                <Link href="/profile/edit" class="profile-edit-button">
                    <DashboardIcon name="edit" />
                    {{ $t('profile.edit') }}
                </Link>
            </section>

            <section class="profile-content">
                <article class="profile-card">
                    <header>
                        <h2>{{ $t('profile.personalInformation') }}</h2>
                        <p>{{ $t('profile.personalInformationHint') }}</p>
                    </header>
                    <dl class="profile-details">
                        <div><dt>{{ $t('profile.email') }}</dt><dd>{{ profile.email }}</dd></div>
                        <div><dt>{{ $t('profile.phone') }}</dt><dd>{{ profile.phone || $t('profile.notSpecified') }}</dd></div>
                    </dl>
                </article>

                <article class="profile-card profile-bio">
                    <header><h2>{{ $t('profile.about') }}</h2></header>
                    <p>{{ profile.bio || $t('profile.emptyBio') }}</p>
                </article>
            </section>

            <ProviderCredentials v-if="profile.role === 'prestataire'" :resume="resume" />
            <ProviderDocuments v-if="profile.role === 'prestataire'" :documents="documents" />

            <section v-if="profile.role === 'prestataire'" class="profile-completed">
                <header>
                    <span class="profile-eyebrow">{{ $t('profile.completedPortfolio') }}</span>
                    <h2>{{ $t('profile.completedMissions') }}</h2>
                    <p>{{ $t('profile.completedMissionsHint') }}</p>
                </header>

                <div v-if="completedMissions.length" class="profile-completed-grid">
                    <article v-for="mission in completedMissions" :key="mission.id" class="profile-completed-card">
                        <div class="profile-mission-gallery">
                            <img v-for="image in mission.images" :key="image.id" :src="image.url" :alt="mission.title" loading="lazy">
                        </div>
                        <div>
                            <small>{{ mission.service?.name }}<template v-if="mission.city"> · {{ mission.city }}</template></small>
                            <h3>{{ mission.title }}</h3>
                            <p v-if="mission.description">{{ mission.description }}</p>
                            <time v-if="mission.date_end" :datetime="mission.date_end">{{ formatDate(mission.date_end) }}</time>
                        </div>
                    </article>
                </div>
                <p v-else class="profile-completed-empty">{{ $t('profile.noCompletedMissions') }}</p>
            </section>
        </main>
    </AppLayout>
</template>

<style scoped lang="scss" src="../../../scss/pages/_profile.scss"></style>
