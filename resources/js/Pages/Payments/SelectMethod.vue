<script setup>
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import DashboardIcon from '@/Components/DashboardIcon.vue'
import { onBeforeUnmount, ref } from 'vue'
const props = defineProps({ mission: { type: Object, required: true }, payments: { type: Array, default: () => [] } })
const method = ref('orange_money')
const processing = ref(false)
const images = ref([])
const previews = ref([])
const imageError = ref('')
const methods = [
    { id: 'orange_money', label: 'Orange Money', icon: 'wallet' },
    { id: 'moov_money', label: 'Moov Money', icon: 'wallet' },
    { id: 'carte', label: 'Carte bancaire', icon: 'wallet' },
    { id: 'paypal', label: 'PayPal', icon: 'coins' },
]
const chooseImages = event => {
    previews.value.forEach(URL.revokeObjectURL)
    const selected = Array.from(event.target.files ?? [])
    imageError.value = ''

    if (selected.length < 1 || selected.length > 5) {
        images.value = []
        previews.value = []
        imageError.value = 'Choisissez entre 1 et 5 images.'
        event.target.value = ''
        return
    }

    images.value = selected
    previews.value = selected.map(URL.createObjectURL)
}
const pay = () => {
    if (!images.value.length) {
        imageError.value = 'Ajoutez au moins une image de la mission réalisée.'
        return
    }

    processing.value = true
    router.post(`/payments/${props.mission.id}`, { method: method.value, images: images.value }, {
        forceFormData: true,
        onFinish: () => processing.value = false,
    })
}
onBeforeUnmount(() => previews.value.forEach(URL.revokeObjectURL))
</script>

<template><Head title="Paiement sécurisé"/><AppLayout><main class="payment-page"><section class="payment-card"><span class="payment-badge"><DashboardIcon name="shield" />Paiement sécurisé</span><h1>Régler la mission</h1><p>{{ mission.title }}</p><strong class="payment-amount">{{ Number(mission.price).toLocaleString('fr-FR') }} FCFA</strong><div class="payment-proof"><h2>Photos de la mission réalisée</h2><p>Ajoutez entre 1 et 5 images. Elles apparaîtront sur le profil du prestataire après confirmation du paiement.</p><label class="payment-proof-picker"><input type="file" accept="image/jpeg,image/png,image/webp" multiple @change="chooseImages"><span>Choisir les images</span><small>JPG, PNG ou WebP · 5 Mo maximum par image</small></label><div v-if="previews.length" class="payment-proof-previews"><img v-for="(preview, index) in previews" :key="preview" :src="preview" :alt="`Aperçu ${index + 1}`"></div><p v-if="imageError || $page.props.errors?.images" class="payment-error">{{ imageError || $page.props.errors.images }}</p></div><div class="payment-methods"><button v-for="item in methods" :key="item.id" type="button" :class="{active:method===item.id}" @click="method=item.id"><DashboardIcon :name="item.icon" /><span>{{ item.label }}</span><DashboardIcon name="completed" /></button></div><p v-if="$page.props.flash?.error" class="payment-error">{{ $page.props.flash.error }}</p><button class="payment-submit" :disabled="processing || !images.length" @click="pay"><DashboardIcon name="shield" />{{ processing ? 'Redirection…' : 'Payer maintenant' }}</button><small>Le paiement est validé uniquement après confirmation sécurisée de la passerelle.</small></section></main></AppLayout></template>
<style lang="scss" src="../../../scss/pages/_payments.scss"></style>
<style scoped>
.payment-card { width: min(720px, 100%); }
.payment-proof { margin: 1.25rem 0; padding: 1rem; border: 1px solid #dce6e0; border-radius: 14px; background: #f8fbf9; }
.payment-proof h2 { margin: 0; font-size: 1.05rem; }
.payment-proof > p { margin: .35rem 0 .9rem; color: #647269; line-height: 1.5; }
.payment-proof-picker { display: grid; gap: .25rem; padding: .9rem; border: 1px dashed #87b49b; border-radius: 11px; color: #145c39; cursor: pointer; font-weight: 800; text-align: center; }
.payment-proof-picker input { position: absolute; width: 1px; height: 1px; overflow: hidden; clip-path: inset(50%); }
.payment-proof-picker small { color: #718078; font-weight: 500; }
.payment-proof-previews { display: grid; grid-template-columns: repeat(5, 1fr); gap: .5rem; margin-top: .8rem; }
.payment-proof-previews img { width: 100%; aspect-ratio: 1; border-radius: 9px; object-fit: cover; }
.payment-submit:disabled { opacity: .55; cursor: not-allowed; }
</style>
