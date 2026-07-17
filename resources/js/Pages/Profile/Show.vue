<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    profile: { type: Object, required: true },
})

const { t } = useI18n()
const fullName = computed(() => `${props.profile.first_name} ${props.profile.last_name}`.trim())
const initials = computed(() => `${props.profile.first_name?.[0] ?? ''}${props.profile.last_name?.[0] ?? ''}`.toUpperCase())
const roleLabel = computed(() => t(`navbar.roles.${props.profile.role === 'prestataire' ? 'provider' : props.profile.role}`))
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
                </div>
                <Link href="/profile/edit" class="profile-edit-button">
                    <i class="fas fa-pen" aria-hidden="true"></i>
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
        </main>
    </AppLayout>
</template>

<style scoped lang="scss" src="../../../scss/pages/_profile.scss"></style>
