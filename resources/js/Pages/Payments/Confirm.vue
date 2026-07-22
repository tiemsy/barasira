<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import DashboardIcon from '@/Components/DashboardIcon.vue'
import { useI18n } from 'vue-i18n'
defineProps({ payment: { type: Object, required: true } })
const { locale } = useI18n()
</script>
<template><Head :title="$t('ui.payments.statusTitle')"/><AppLayout><main class="payment-page"><section class="payment-card payment-result"><DashboardIcon :name="payment.status === 'effectue' ? 'completed' : 'calendar'" :class="payment.status === 'effectue' ? 'is-success' : 'is-pending'" /><h1>{{ payment.status === 'effectue' ? $t('ui.payments.confirmed') : $t('ui.payments.pending') }}</h1><p>{{ payment.status === 'effectue' ? $t('ui.payments.confirmedText') : $t('ui.payments.pendingText') }}</p><strong class="payment-amount">{{ Number(payment.amount).toLocaleString(locale) }} FCFA</strong><Link href="/missions/index" class="payment-submit">{{ $t('ui.payments.backMissions') }}</Link></section></main></AppLayout></template>
<style lang="scss" src="../../../scss/pages/_payments.scss"></style>
