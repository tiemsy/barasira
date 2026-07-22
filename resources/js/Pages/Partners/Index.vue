<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import DashboardIcon from '@/Components/DashboardIcon.vue'
defineProps({ partners: { type: Array, default: () => [] } })
</script>
<template>
    <Head :title="$t('partners.metaTitle')" />
    <AppLayout><main class="partners-page"><section class="partners-hero"><div><span>{{ $t('partners.eyebrow') }}</span><h1>{{ $t('partners.title') }}</h1><p>{{ $t('partners.subtitle') }}</p><Link href="/partners/sponsoring" class="partner-sponsorship-link">{{ $t('sponsorship.cta') }}</Link></div></section><section class="partners-content"><div v-if="partners.length" class="partners-grid"><article v-for="partner in partners" :key="partner.id" class="partner-card"><a v-if="partner.website_url" :href="partner.website_url" target="_blank" rel="noopener noreferrer" class="partner-card__logo"><img v-if="partner.logo_url" :src="partner.logo_url" :alt="partner.company_name"><DashboardIcon v-else name="building" /></a><div v-else class="partner-card__logo"><img v-if="partner.logo_url" :src="partner.logo_url" :alt="partner.company_name"><DashboardIcon v-else name="building" /></div><h2>{{ partner.company_name }}</h2><p v-if="partner.description">{{ partner.description }}</p><a v-if="partner.website_url" :href="partner.website_url" target="_blank" rel="noopener noreferrer" class="partner-card__link">{{ $t('partners.visitWebsite') }} <DashboardIcon name="external" /></a></article></div><div v-else class="partners-empty"><DashboardIcon name="building" /><h2>{{ $t('partners.emptyTitle') }}</h2><p>{{ $t('partners.emptyText') }}</p></div></section></main></AppLayout>
</template>
<style lang="scss" src="../../../scss/pages/_partners.scss"></style>
