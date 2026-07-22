<script setup>
import { computed, reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'
import DashboardIcon from '@/Components/DashboardIcon.vue'
import { useConfirmDialog } from '@/composables/useConfirmDialog'

const props = defineProps({ partners: { type: Object, required: true }, filters: { type: Object, default: () => ({}) } })
const filters = reactive({ search: props.filters.search ?? '', status: props.filters.status ?? '' })
const exportUrl = computed(() => `/admin/exports/partners?${new URLSearchParams(Object.entries(filters).filter(([, value]) => value)).toString()}`)
const { t } = useI18n()
const { confirm } = useConfirmDialog()
const applyFilters = () => router.get('/admin/partners', filters, { preserveState: true, replace: true })
const resetFilters = () => { Object.assign(filters, { search: '', status: '' }); applyFilters() }
async function removePartner(partner) {
    if (!await confirm({ title: t('confirmDialog.deleteTitle'), message: t('adminPartners.deleteConfirm', { name: partner.company_name }), confirmLabel: t('confirmDialog.delete') })) return
    router.delete(`/admin/partners/${partner.id}`, { preserveScroll: true })
}
</script>

<template>
    <Head :title="$t('adminPartners.metaTitle')" />
    <AppLayout>
        <main class="admin-users-page admin-partners-page">
            <section class="admin-users-hero"><div><span>{{ $t('adminPartners.eyebrow') }}</span><h1>{{ $t('adminPartners.title') }}</h1><p>{{ $t('adminPartners.subtitle') }}</p></div><Link href="/admin/partners/create" class="admin-users-primary"><DashboardIcon name="plus" />{{ $t('adminPartners.add') }}</Link></section>
            <section class="admin-users-card">
                <form class="admin-users-filters" @submit.prevent="applyFilters"><label><DashboardIcon name="search" /><input v-model.trim="filters.search" type="search" :placeholder="$t('adminPartners.searchPlaceholder')"></label><select v-model="filters.status"><option value="">{{ $t('adminPartners.allStatuses') }}</option><option value="published">{{ $t('adminPartners.published') }}</option><option value="draft">{{ $t('adminPartners.draft') }}</option></select><button type="submit">{{ $t('adminPartners.filter') }}</button><button type="button" class="is-ghost" @click="resetFilters">{{ $t('adminPartners.reset') }}</button><a :href="exportUrl" class="admin-export">{{ $t('ui.common.exportExcel') }}</a></form>
                <div v-if="partners.data.length" class="admin-users-table-wrap"><table class="admin-users-table admin-partners-table"><thead><tr><th>{{ $t('adminPartners.company') }}</th><th>{{ $t('adminPartners.contact') }}</th><th>{{ $t('adminPartners.website') }}</th><th>{{ $t('adminPartners.status') }}</th><th><span class="sr-only">{{ $t('adminPartners.actions') }}</span></th></tr></thead><tbody><tr v-for="partner in partners.data" :key="partner.id"><td><div class="partner-admin-company"><span><img v-if="partner.logo_url" :src="partner.logo_url" alt=""><DashboardIcon v-else name="building" /></span><div><strong>{{ partner.company_name }}</strong><small>{{ partner.company_email || $t('adminPartners.notSpecified') }}</small></div></div></td><td><strong>{{ partner.contact_name }}</strong><small>{{ partner.contact_email }}</small></td><td><a v-if="partner.website_url" :href="partner.website_url" target="_blank" rel="noopener noreferrer">{{ $t('adminPartners.visit') }} <DashboardIcon name="external" /></a><span v-else>—</span></td><td><span :class="['admin-users-status', partner.is_published ? 'is-verified' : 'is-pending']">{{ partner.is_published ? $t('adminPartners.published') : $t('adminPartners.draft') }}</span></td><td><div class="admin-users-actions"><Link class="is-edit" :href="`/admin/partners/${partner.id}/edit`"><DashboardIcon name="edit" /></Link><button class="is-delete" type="button" @click="removePartner(partner)"><DashboardIcon name="delete" /></button></div></td></tr></tbody></table></div>
                <p v-else class="admin-users-empty">{{ $t('adminPartners.empty') }}</p>
                <nav v-if="partners.links?.length > 3" class="admin-users-pagination"><Link v-for="link in partners.links" :key="link.label" :href="link.url || ''" :class="{ active: link.active, disabled: !link.url }" preserve-scroll v-html="link.label" /></nav>
            </section>
        </main>
    </AppLayout>
</template>
<style lang="scss" src="../../../../scss/pages/admin/_partners.scss"></style>
