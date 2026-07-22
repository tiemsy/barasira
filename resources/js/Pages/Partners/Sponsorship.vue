<script setup>
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'
import DashboardIcon from '@/Components/DashboardIcon.vue'

const props = defineProps({ plans: { type: Array, default: () => [] }, categories: { type: Array, default: () => [] } })
const { locale } = useI18n()
const form = useForm({ company_name: '', contact_name: '', email: '', phone: '', website_url: '', category: '', other_category: '', plan: props.plans[1]?.id ?? props.plans[0]?.id ?? '', message: '', consent: false })
const selectedPlan = computed(() => props.plans.find(plan => plan.id === form.plan))
const money = amount => new Intl.NumberFormat(locale.value).format(Number(amount))
const submit = () => form.post('/partners/sponsoring', { preserveScroll: true })
</script>

<template>
    <AppLayout><main class="sponsorship-page">
        <section class="sponsorship-hero"><div><span>{{ $t('sponsorship.eyebrow') }}</span><h1>{{ $t('sponsorship.title') }}</h1><p>{{ $t('sponsorship.subtitle') }}</p></div></section>
        <section class="sponsorship-content">
            <header><h2>{{ $t('sponsorship.plansTitle') }}</h2><p>{{ $t('sponsorship.plansText') }}</p></header>
            <div class="sponsorship-plans" role="radiogroup" :aria-label="$t('sponsorship.plansTitle')">
                <label v-for="plan in plans" :key="plan.id" :class="{ active: form.plan === plan.id }">
                    <input v-model="form.plan" type="radio" name="sponsorship-plan" :value="plan.id">
                    <span v-if="plan.id === 'month'">{{ $t('sponsorship.recommended') }}</span><DashboardIcon name="sponsor" />
                    <h3>{{ $t(`sponsorship.plans.${plan.id}.name`) }}</h3><strong>{{ money(plan.price) }} <small>FCFA</small></strong>
                    <p>{{ $t(`sponsorship.plans.${plan.id}.description`) }}</p><em>{{ $t('sponsorship.choosePlan') }}</em>
                </label>
            </div>
            <small v-if="form.errors.plan" class="sponsorship-error">{{ form.errors.plan }}</small>
            <div class="sponsorship-form-layout">
                <div class="sponsorship-benefits"><span>{{ $t('sponsorship.benefitsEyebrow') }}</span><h2>{{ $t('sponsorship.benefitsTitle') }}</h2><ul><li v-for="item in 4" :key="item"><DashboardIcon name="completed" />{{ $t(`sponsorship.benefits.${item}`) }}</li></ul><div v-if="selectedPlan"><small>{{ $t('sponsorship.selected') }}</small><strong>{{ $t(`sponsorship.plans.${selectedPlan.id}.name`) }} · {{ money(selectedPlan.price) }} FCFA</strong></div></div>
                <form @submit.prevent="submit"><h2>{{ $t('sponsorship.formTitle') }}</h2><p>{{ $t('sponsorship.formText') }}</p>
                    <div class="sponsorship-form-grid">
                        <label><span>{{ $t('sponsorship.company') }} *</span><input v-model.trim="form.company_name" required maxlength="150"><small v-if="form.errors.company_name">{{ form.errors.company_name }}</small></label>
                        <label><span>{{ $t('sponsorship.contact') }} *</span><input v-model.trim="form.contact_name" required maxlength="150"><small v-if="form.errors.contact_name">{{ form.errors.contact_name }}</small></label>
                        <label><span>{{ $t('auth.email') }} *</span><input v-model.trim="form.email" type="email" required><small v-if="form.errors.email">{{ form.errors.email }}</small></label>
                        <label><span>{{ $t('auth.phone') }} *</span><input v-model.trim="form.phone" type="tel" required><small v-if="form.errors.phone">{{ form.errors.phone }}</small></label>
                        <label><span>{{ $t('sponsorship.website') }}</span><input v-model.trim="form.website_url" type="url" placeholder="https://"><small v-if="form.errors.website_url">{{ form.errors.website_url }}</small></label>
                        <label><span>{{ $t('sponsorship.category') }} *</span><select v-model="form.category" required><option value="" disabled>{{ $t('sponsorship.selectCategory') }}</option><option v-for="category in categories" :key="category" :value="category">{{ $t(`sponsorship.categories.${category}`) }}</option></select><small v-if="form.errors.category">{{ form.errors.category }}</small></label>
                    </div>
                    <label v-if="form.category === 'other'"><span>{{ $t('sponsorship.otherCategory') }} *</span><input v-model.trim="form.other_category" required maxlength="100"><small v-if="form.errors.other_category">{{ form.errors.other_category }}</small></label>
                    <label><span>{{ $t('sponsorship.message') }}</span><textarea v-model.trim="form.message" rows="5" maxlength="2000" :placeholder="$t('sponsorship.messagePlaceholder')"></textarea><small v-if="form.errors.message">{{ form.errors.message }}</small></label>
                    <label class="sponsorship-consent"><input v-model="form.consent" type="checkbox" required><span>{{ $t('sponsorship.consent') }}</span></label><small v-if="form.errors.consent" class="sponsorship-error">{{ form.errors.consent }}</small>
                    <button type="submit" :disabled="form.processing"><DashboardIcon name="mail" />{{ form.processing ? $t('sponsorship.sending') : $t('sponsorship.send') }}</button>
                </form>
            </div>
        </section>
    </main></AppLayout>
</template>

<style scoped>
.sponsorship-page{background:#f7faf8;color:#16271e}.sponsorship-hero{padding:5rem 1rem;background:linear-gradient(120deg,#0d3d31,#167054);color:#fff;text-align:center}.sponsorship-hero div,.sponsorship-content{width:min(1120px,92%);margin:auto}.sponsorship-hero span,.sponsorship-benefits>span{color:#efc568;font-weight:900;text-transform:uppercase;letter-spacing:.12em}.sponsorship-hero h1{font-size:clamp(2.4rem,5vw,4.5rem);margin:.7rem 0}.sponsorship-hero p{max-width:720px;margin:auto;color:#d7ebe2;font-size:1.1rem}.sponsorship-content{padding:4rem 0}.sponsorship-content>header{text-align:center;margin-bottom:2rem}.sponsorship-plans{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem}.sponsorship-plans>label{position:relative;display:block;padding:2rem 1.4rem;border:2px solid #e0e9e3;border-radius:20px;background:#fff;cursor:pointer;text-align:left;transition:.2s}.sponsorship-plans>label:hover,.sponsorship-plans>label.active{border-color:#167054;transform:translateY(-4px);box-shadow:0 20px 45px rgba(13,61,49,.12)}.sponsorship-plans>label:focus-within{outline:3px solid rgba(22,112,84,.25);outline-offset:3px}.sponsorship-plans input{position:absolute;top:1rem;left:1rem;width:18px;height:18px;accent-color:#167054}.sponsorship-plans label>span{position:absolute;right:1rem;top:1rem;padding:.3rem .6rem;border-radius:999px;background:#fff1c9;color:#795500;font-size:.7rem;font-weight:900}.sponsorship-plans svg{width:30px;margin-top:1rem;color:#167054}.sponsorship-plans h3{font-size:1.35rem}.sponsorship-plans strong{display:block;color:#167054;font-size:1.8rem}.sponsorship-plans strong small{font-size:.7rem}.sponsorship-plans p{min-height:48px;color:#627168}.sponsorship-plans em{color:#167054;font-style:normal;font-weight:800}.sponsorship-form-layout{display:grid;grid-template-columns:.8fr 1.2fr;gap:2rem;margin-top:4rem}.sponsorship-benefits{padding:2rem;border-radius:22px;background:#102e26;color:#fff}.sponsorship-benefits ul{list-style:none;padding:0;display:grid;gap:1rem}.sponsorship-benefits li{display:flex;gap:.7rem;align-items:center;color:#d9e8e1}.sponsorship-benefits li svg{width:20px;color:#efc568}.sponsorship-benefits>div{display:grid;margin-top:2rem;padding:1rem;border-radius:12px;background:rgba(255,255,255,.08)}.sponsorship-form-layout form{padding:2rem;border:1px solid #e0e9e3;border-radius:22px;background:#fff;box-shadow:0 18px 45px rgba(13,61,49,.07)}.sponsorship-form-grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);column-gap:1.5rem;row-gap:.5rem}.sponsorship-form-layout label{display:grid;min-width:0;gap:.5rem;margin-bottom:1.25rem;font-weight:700}.sponsorship-form-layout input,.sponsorship-form-layout select,.sponsorship-form-layout textarea{box-sizing:border-box;width:100%;padding:.85rem 1rem;border:1px solid #cbd8d0;border-radius:9px;font:inherit}.sponsorship-form-layout label small,.sponsorship-error{color:#b42318}.sponsorship-consent{grid-template-columns:auto 1fr!important;align-items:start;font-weight:500!important}.sponsorship-consent input{width:auto}.sponsorship-form-layout form>button{display:flex;align-items:center;justify-content:center;gap:.6rem;width:100%;padding:.9rem;border:0;border-radius:10px;background:#167054;color:#fff;font-weight:900;cursor:pointer}.sponsorship-form-layout form>button:disabled{opacity:.55}@media(max-width:800px){.sponsorship-plans,.sponsorship-form-layout,.sponsorship-form-grid{grid-template-columns:1fr}.sponsorship-plans p{min-height:0}}
</style>
