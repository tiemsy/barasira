<script setup>
import { computed, onMounted, ref } from 'vue'
import { useCookieConsent } from '@/composables/useCookieConsent'

const consent = useCookieConsent()
const marketing = ref(false)
const showBanner = computed(() => consent.state.ready && !consent.hasChoice() && !consent.state.preferencesOpen)

onMounted(() => {
    consent.initialize()
    marketing.value = consent.state.marketing
    window.addEventListener('barasira:cookie-preferences', () => {
        marketing.value = consent.state.marketing
        consent.openPreferences()
    })
})
</script>

<template>
    <section v-if="showBanner" class="cookie-banner" role="dialog" aria-live="polite" :aria-label="$t('cookies.title')">
        <div><strong>{{ $t('cookies.title') }}</strong><p>{{ $t('cookies.description') }}</p></div>
        <div class="cookie-actions"><button type="button" class="is-secondary" @click="consent.rejectOptional">{{ $t('cookies.reject') }}</button><button type="button" class="is-secondary" @click="consent.openPreferences">{{ $t('cookies.customize') }}</button><button type="button" @click="consent.acceptAll">{{ $t('cookies.accept') }}</button></div>
    </section>

    <div v-if="consent.state.preferencesOpen" class="cookie-modal-backdrop" @click.self="consent.closePreferences">
        <section class="cookie-modal" role="dialog" aria-modal="true" :aria-label="$t('cookies.settingsTitle')">
            <header><div><span>{{ $t('cookies.eyebrow') }}</span><h2>{{ $t('cookies.settingsTitle') }}</h2></div><button type="button" class="cookie-close" :aria-label="$t('cookies.close')" @click="consent.closePreferences">×</button></header>
            <p>{{ $t('cookies.settingsText') }}</p>
            <article><div><strong>{{ $t('cookies.necessaryTitle') }}</strong><p>{{ $t('cookies.necessaryText') }}</p></div><span>{{ $t('cookies.alwaysActive') }}</span></article>
            <article><div><strong>{{ $t('cookies.marketingTitle') }}</strong><p>{{ $t('cookies.marketingText') }}</p></div><label class="cookie-switch"><input v-model="marketing" type="checkbox"><span></span><b class="sr-only">{{ $t('cookies.marketingTitle') }}</b></label></article>
            <footer><button type="button" class="is-secondary" @click="consent.rejectOptional">{{ $t('cookies.reject') }}</button><button type="button" @click="consent.savePreferences(marketing)">{{ $t('cookies.save') }}</button></footer>
        </section>
    </div>
</template>

<style scoped>
.cookie-banner{position:fixed;z-index:10000;right:1rem;bottom:1rem;left:1rem;display:flex;max-width:1120px;align-items:center;justify-content:space-between;gap:1.5rem;margin:auto;padding:1.25rem 1.4rem;border:1px solid #d9e4dd;border-radius:16px;background:#fff;color:#17251e;box-shadow:0 18px 55px rgba(12,61,39,.2)}.cookie-banner strong{font-size:1.05rem}.cookie-banner p{max-width:650px;margin:.35rem 0 0;color:#5e6d64}.cookie-actions,.cookie-modal footer{display:flex;gap:.65rem;flex-wrap:wrap}.cookie-banner button,.cookie-modal button{padding:.72rem .9rem;border:1px solid #176b45;border-radius:9px;background:#176b45;color:#fff;font:inherit;font-weight:800;cursor:pointer}.cookie-banner .is-secondary,.cookie-modal .is-secondary{background:#fff;color:#176b45}.cookie-modal-backdrop{position:fixed;z-index:10001;inset:0;display:grid;place-items:center;padding:1rem;background:rgba(8,27,18,.58)}.cookie-modal{width:min(620px,100%);max-height:calc(100vh - 2rem);overflow:auto;padding:1.5rem;border-radius:18px;background:#fff;color:#17251e;box-shadow:0 24px 70px rgba(0,0,0,.3)}.cookie-modal header{display:flex;align-items:start;justify-content:space-between;gap:1rem}.cookie-modal header span{color:#176b45;font-size:.72rem;font-weight:900;letter-spacing:.1em;text-transform:uppercase}.cookie-modal h2{margin:.3rem 0}.cookie-close{padding:.2rem .55rem!important;border:0!important;background:transparent!important;color:#44544b!important;font-size:1.7rem!important}.cookie-modal>p,.cookie-modal article p{color:#65746b}.cookie-modal article{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem 0;border-top:1px solid #e3ebe6}.cookie-modal article p{margin:.25rem 0 0;font-size:.9rem}.cookie-modal article>span{color:#176b45;font-size:.78rem;font-weight:800;white-space:nowrap}.cookie-modal footer{justify-content:flex-end;padding-top:1rem;border-top:1px solid #e3ebe6}.cookie-switch input{position:absolute;opacity:0}.cookie-switch>span{display:block;width:46px;height:26px;border-radius:999px;background:#bac6bf;cursor:pointer;transition:.2s}.cookie-switch>span:after{display:block;width:20px;height:20px;margin:3px;border-radius:50%;background:#fff;content:"";transition:.2s}.cookie-switch input:checked+span{background:#176b45}.cookie-switch input:checked+span:after{transform:translateX(20px)}@media(max-width:760px){.cookie-banner{align-items:stretch;flex-direction:column}.cookie-actions{display:grid;grid-template-columns:1fr}.cookie-modal article{align-items:flex-start}}
</style>
