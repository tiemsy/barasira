<script setup>
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
const props = defineProps({ mission: { type: Object, required: true }, payments: { type: Array, default: () => [] } })
const method = ref('orange_money')
const processing = ref(false)
const methods = [
    { id: 'orange_money', label: 'Orange Money', icon: 'fas fa-mobile-screen' },
    { id: 'moov_money', label: 'Moov Money', icon: 'fas fa-mobile-screen-button' },
    { id: 'carte', label: 'Carte bancaire', icon: 'fas fa-credit-card' },
    { id: 'paypal', label: 'PayPal', icon: 'fab fa-paypal' },
]
const pay = () => { processing.value = true; router.post(`/payments/${props.mission.id}`, { method: method.value }, { onFinish: () => processing.value = false }) }
</script>

<template><Head title="Paiement sécurisé"/><AppLayout><main class="payment-page"><section class="payment-card"><span class="payment-badge"><i class="fas fa-lock"></i>Paiement sécurisé</span><h1>Régler la mission</h1><p>{{ mission.title }}</p><strong class="payment-amount">{{ Number(mission.price).toLocaleString('fr-FR') }} FCFA</strong><div class="payment-methods"><button v-for="item in methods" :key="item.id" type="button" :class="{active:method===item.id}" @click="method=item.id"><i :class="item.icon"></i><span>{{ item.label }}</span><i class="fas fa-circle-check"></i></button></div><p v-if="$page.props.flash?.error" class="payment-error">{{ $page.props.flash.error }}</p><button class="payment-submit" :disabled="processing" @click="pay"><i class="fas fa-shield-halved"></i>{{ processing ? 'Redirection…' : 'Payer maintenant' }}</button><small>Le paiement est validé uniquement après confirmation sécurisée de la passerelle.</small></section></main></AppLayout></template>
<style lang="scss" src="../../../scss/pages/_payments.scss"></style>
