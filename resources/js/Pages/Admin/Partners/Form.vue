<script setup>
import { computed, ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({ partner: { type: Object, default: null }, promotion: { type: Object, default: null } })
const editing = computed(() => Boolean(props.partner?.id))
const logoPreview = ref(props.partner?.logo_url ?? null)
const dateTimeInput = value => {
    if (!value) return ''
    const date = new Date(value)
    const localDate = new Date(date.getTime() - date.getTimezoneOffset() * 60000)
    return localDate.toISOString().slice(0, 16)
}
const utcDateTime = value => value ? new Date(value).toISOString() : ''
const form = useForm({ company_name: props.partner?.company_name ?? '', description: props.partner?.description ?? '', logo: null, website_url: props.partner?.website_url ?? '', company_email: props.partner?.company_email ?? '', company_phone: props.partner?.company_phone ?? '', address: props.partner?.address ?? '', contact_name: props.partner?.contact_name ?? '', contact_position: props.partner?.contact_position ?? '', contact_email: props.partner?.contact_email ?? '', contact_phone: props.partner?.contact_phone ?? '', is_published: props.partner?.is_published ?? false, display_order: props.partner?.display_order ?? 0, promotion_id: props.promotion?.id ?? null, promotion_amount: props.promotion?.paid_amount ?? '', promotion_starts_at: dateTimeInput(props.promotion?.starts_at), promotion_ends_at: dateTimeInput(props.promotion?.ends_at) })
function selectLogo(event) {
    form.logo = event.target.files?.[0] ?? null
    if (form.logo) logoPreview.value = URL.createObjectURL(form.logo)
}
function submit() {
    form.transform(data => ({
        ...data,
        ...(editing.value ? { _method: 'put' } : {}),
        promotion_starts_at: utcDateTime(data.promotion_starts_at),
        promotion_ends_at: utcDateTime(data.promotion_ends_at),
    }))
    if (editing.value) form.post(`/admin/partners/${props.partner.id}`, { forceFormData: true })
    else form.post('/admin/partners', { forceFormData: true })
}
</script>

<template>
    <Head :title="editing ? $t('adminPartners.editTitle') : $t('adminPartners.createTitle')" />
    <AppLayout><main class="admin-user-form-page admin-partner-form-page"><header class="admin-user-form-header"><div><span>{{ $t('adminPartners.eyebrow') }}</span><h1>{{ editing ? $t('adminPartners.editTitle') : $t('adminPartners.createTitle') }}</h1><p>{{ $t('adminPartners.formSubtitle') }}</p></div><Link href="/admin/partners">{{ $t('adminPartners.back') }}</Link></header>
        <form class="admin-user-form-card" @submit.prevent="submit">
            <section><header><h2>{{ $t('adminPartners.companyInfo') }}</h2><p>{{ $t('adminPartners.companyHint') }}</p></header><div class="admin-user-form-grid">
                <label><span>{{ $t('adminPartners.companyName') }}</span><input v-model.trim="form.company_name" required maxlength="150"><small v-if="form.errors.company_name">{{ form.errors.company_name }}</small></label>
                <label><span>{{ $t('adminPartners.website') }}</span><input v-model.trim="form.website_url" type="url" placeholder="https://"><small v-if="form.errors.website_url">{{ form.errors.website_url }}</small></label>
                <label><span>{{ $t('adminPartners.companyEmail') }}</span><input v-model.trim="form.company_email" type="email"><small v-if="form.errors.company_email">{{ form.errors.company_email }}</small></label>
                <label><span>{{ $t('adminPartners.companyPhone') }}</span><input v-model.trim="form.company_phone" type="tel"><small v-if="form.errors.company_phone">{{ form.errors.company_phone }}</small></label>
                <label class="full"><span>{{ $t('adminPartners.address') }}</span><input v-model.trim="form.address" maxlength="255"><small v-if="form.errors.address">{{ form.errors.address }}</small></label>
                <label class="full"><span>{{ $t('adminPartners.description') }}</span><textarea v-model.trim="form.description" rows="5" maxlength="3000"></textarea><small v-if="form.errors.description">{{ form.errors.description }}</small></label>
                <label class="full partner-logo-field"><span>{{ $t('adminPartners.logo') }}</span><div><span class="partner-logo-preview"><img v-if="logoPreview" :src="logoPreview" alt=""><span v-else>{{ $t('adminPartners.noLogo') }}</span></span><input type="file" accept="image/png,image/jpeg,image/webp" @change="selectLogo"></div><small>{{ $t('adminPartners.logoHint') }}</small><small v-if="form.errors.logo">{{ form.errors.logo }}</small></label>
            </div></section>
            <section><header><h2>{{ $t('adminPartners.contactInfo') }}</h2><p>{{ $t('adminPartners.contactHint') }}</p></header><div class="admin-user-form-grid">
                <label><span>{{ $t('adminPartners.contactName') }}</span><input v-model.trim="form.contact_name" required maxlength="150"><small v-if="form.errors.contact_name">{{ form.errors.contact_name }}</small></label>
                <label><span>{{ $t('adminPartners.contactPosition') }}</span><input v-model.trim="form.contact_position" maxlength="150"><small v-if="form.errors.contact_position">{{ form.errors.contact_position }}</small></label>
                <label><span>{{ $t('adminPartners.contactEmail') }}</span><input v-model.trim="form.contact_email" required type="email"><small v-if="form.errors.contact_email">{{ form.errors.contact_email }}</small></label>
                <label><span>{{ $t('adminPartners.contactPhone') }}</span><input v-model.trim="form.contact_phone" type="tel"><small v-if="form.errors.contact_phone">{{ form.errors.contact_phone }}</small></label>
            </div></section>
            <section><header><h2>{{ $t('adminPartners.visibility') }}</h2></header><div class="admin-user-form-grid"><label><span>{{ $t('adminPartners.order') }}</span><input v-model.number="form.display_order" type="number" min="0" max="9999"><small v-if="form.errors.display_order">{{ form.errors.display_order }}</small></label><label class="admin-user-check"><input v-model="form.is_published" type="checkbox"><span>{{ $t('adminPartners.publish') }}</span></label></div></section>
            <section><header><h2>{{ $t('adminPartners.promotionTitle') }}</h2><p>{{ $t('adminPartners.promotionHint') }}</p></header><div class="admin-user-form-grid">
                <label><span>{{ $t('adminPartners.promotionAmount') }}</span><input v-model.number="form.promotion_amount" type="number" min="1" step="1" placeholder="500000"><small v-if="form.errors.promotion_amount">{{ form.errors.promotion_amount }}</small></label>
                <span></span>
                <label><span>{{ $t('adminPartners.promotionStart') }}</span><input v-model="form.promotion_starts_at" type="datetime-local"><small v-if="form.errors.promotion_starts_at">{{ form.errors.promotion_starts_at }}</small></label>
                <label><span>{{ $t('adminPartners.promotionEnd') }}</span><input v-model="form.promotion_ends_at" type="datetime-local"><small v-if="form.errors.promotion_ends_at">{{ form.errors.promotion_ends_at }}</small></label>
            </div><p class="partner-promotion-note">{{ $t('adminPartners.promotionRule') }}</p></section>
            <footer><Link href="/admin/partners">{{ $t('profile.cancel') }}</Link><button type="submit" :disabled="form.processing">{{ form.processing ? $t('profile.saving') : $t('profile.save') }}</button></footer>
        </form></main></AppLayout>
</template>
<style lang="scss" src="../../../../scss/pages/admin/_partners.scss"></style>
