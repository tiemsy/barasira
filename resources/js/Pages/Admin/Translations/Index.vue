<script setup>
import { computed, reactive, ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { api } from '@/lib/api'
import AppLayout from '@/Layouts/AppLayout.vue'
import DashboardIcon from '@/Components/DashboardIcon.vue'

const props = defineProps({ locales: { type: Array, default: () => ['fr', 'en', 'bm'] }, provider: { type: String, default: '' } })
const { t } = useI18n()
const sourceLocale = ref('fr')
const text = ref('')
const results = reactive({})
const loading = ref(false)
const error = ref('')
const copied = ref('')
const targets = computed(() => props.locales.filter(locale => locale !== sourceLocale.value))

async function translateAll() {
    if (!text.value.trim()) return
    loading.value = true
    error.value = ''
    copied.value = ''
    Object.keys(results).forEach(key => delete results[key])
    try {
        const responses = await Promise.all(targets.value.map(target => api.post('/ai/translate', {
            text: text.value,
            source_locale: sourceLocale.value,
            target_locale: target,
            context: { type: 'superadmin_workspace' },
        })))
        responses.forEach(({ data }) => { results[data.target_locale] = data.translation })
    } catch (exception) {
        error.value = exception.response?.data?.message || t('adminTranslations.error')
    } finally {
        loading.value = false
    }
}

async function copy(locale) {
    await navigator.clipboard.writeText(results[locale] || '')
    copied.value = locale
}
</script>

<template>
    <Head :title="$t('adminTranslations.metaTitle')" />
    <AppLayout><main class="translation-workspace">
        <header><div><span><DashboardIcon name="language" />{{ $t('adminTranslations.eyebrow') }}</span><h1>{{ $t('adminTranslations.title') }}</h1><p>{{ $t('adminTranslations.subtitle') }}</p></div><Link href="/admin/dashboard"><DashboardIcon name="arrow-left" />{{ $t('adminTranslations.back') }}</Link></header>
        <section class="translation-workspace__panel">
            <div class="translation-workspace__source">
                <div><label>{{ $t('adminTranslations.sourceLanguage') }}<select v-model="sourceLocale"><option v-for="locale in locales" :key="locale" :value="locale">{{ $t(`adminTranslations.languages.${locale}`) }}</option></select></label><small>{{ $t('adminTranslations.provider', { provider }) }}</small></div>
                <label>{{ $t('adminTranslations.sourceText') }}<textarea v-model="text" maxlength="10000" rows="10" :placeholder="$t('adminTranslations.placeholder')" /></label>
                <div class="translation-workspace__actions"><span>{{ text.length }} / 10 000</span><button type="button" :disabled="loading || !text.trim()" @click="translateAll"><DashboardIcon name="language" />{{ loading ? $t('adminTranslations.loading') : $t('adminTranslations.translateAll') }}</button></div>
                <p v-if="error" class="translation-workspace__error">{{ error }}</p>
            </div>
            <div class="translation-workspace__results">
                <article v-for="locale in targets" :key="locale"><header><strong>{{ $t(`adminTranslations.languages.${locale}`) }}</strong><button v-if="results[locale]" type="button" @click="copy(locale)">{{ copied === locale ? $t('adminTranslations.copied') : $t('adminTranslations.copy') }}</button></header><textarea v-model="results[locale]" rows="9" :placeholder="$t('adminTranslations.resultPlaceholder')" /></article>
            </div>
        </section>
        <p class="translation-workspace__notice"><DashboardIcon name="lock" />{{ $t('adminTranslations.notice') }}</p>
    </main></AppLayout>
</template>

<style scoped>
.translation-workspace{min-height:100vh;padding:3rem max(1rem,calc((100vw - 1200px)/2)) 4rem;background:#f4f7f5;color:#17251e}.translation-workspace>header{display:flex;align-items:flex-end;justify-content:space-between;gap:2rem;margin-bottom:1rem;padding:2rem;border-radius:20px;background:linear-gradient(125deg,#0b3522,#176c43);color:#fff}.translation-workspace>header span,.translation-workspace>header>a{display:inline-flex;align-items:center;gap:.5rem}.translation-workspace>header h1{margin:.5rem 0}.translation-workspace>header p{margin:0;color:#d5e8dd}.translation-workspace>header>a{padding:.75rem 1rem;border-radius:9px;background:#fff;color:#145c39;font-weight:800;text-decoration:none}.translation-workspace__panel{padding:1.4rem;border:1px solid #dfe8e2;border-radius:16px;background:#fff}.translation-workspace__source>div:first-child{display:flex;align-items:flex-end;justify-content:space-between;gap:1rem}.translation-workspace label{display:grid;gap:.4rem;font-weight:800}.translation-workspace select,.translation-workspace textarea{box-sizing:border-box;width:100%;padding:.75rem;border:1px solid #ccd8d1;border-radius:9px;background:#fff;font:inherit}.translation-workspace__source>label{margin-top:1rem}.translation-workspace__actions{display:flex;align-items:center;justify-content:space-between;margin-top:.7rem;color:#718078}.translation-workspace__actions button{display:inline-flex;align-items:center;gap:.5rem;padding:.75rem 1rem;border:0;border-radius:9px;background:#176b45;color:#fff;font-weight:800;cursor:pointer}.translation-workspace button:disabled{cursor:not-allowed;opacity:.5}.translation-workspace__results{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem;margin-top:1.4rem}.translation-workspace__results article{padding:1rem;border:1px solid #dde7e1;border-radius:12px;background:#f9fbfa}.translation-workspace__results article header{display:flex;align-items:center;justify-content:space-between;margin-bottom:.6rem}.translation-workspace__results article button{border:0;background:transparent;color:#176b45;font-weight:800;cursor:pointer}.translation-workspace__error{color:#b42318;font-weight:700}.translation-workspace__notice{display:flex;align-items:center;gap:.5rem;color:#68786e;font-size:.8rem}@media(max-width:700px){.translation-workspace>header{align-items:stretch;flex-direction:column}.translation-workspace__results{grid-template-columns:1fr}.translation-workspace__source>div:first-child{align-items:stretch;flex-direction:column}}
</style>
