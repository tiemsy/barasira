<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
defineProps({ payment: { type: Object, required: true } })
</script>
<template><Head title="Statut du paiement"/><AppLayout><main class="payment-page"><section class="payment-card payment-result"><i :class="payment.status==='effectue'?'fas fa-circle-check is-success':'fas fa-clock is-pending'"></i><h1>{{ payment.status === 'effectue' ? 'Paiement confirmé' : 'Paiement en cours de vérification' }}</h1><p>{{ payment.status === 'effectue' ? 'La passerelle a confirmé votre règlement.' : 'Le statut sera mis à jour dès réception de la confirmation sécurisée.' }}</p><strong class="payment-amount">{{ Number(payment.amount).toLocaleString('fr-FR') }} FCFA</strong><Link href="/missions/index" class="payment-submit">Retour aux missions</Link></section></main></AppLayout></template>
<style lang="scss" src="../../../scss/pages/_payments.scss"></style>
