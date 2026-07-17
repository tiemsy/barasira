<script setup>
import { reactive } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useConfirmDialog } from '@/composables/useConfirmDialog'

const props = defineProps({
    services: { type: Object, required: true },
    categories: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
})
const page = usePage()
const { t } = useI18n()
const { confirm } = useConfirmDialog()
const filters = reactive({ search: props.filters.search ?? '', category: props.filters.category ?? '', status: props.filters.status ?? '' })
const providerName = service => `${service.user?.first_name ?? ''} ${service.user?.last_name ?? ''}`.trim()
const formatPrice = value => new Intl.NumberFormat().format(value ?? 0) + ' FCFA'

function applyFilters() {
    router.get('/admin/services', filters, { preserveState: true, replace: true })
}

function resetFilters() {
    Object.assign(filters, { search: '', category: '', status: '' })
    applyFilters()
}

async function removeService(service) {
    if (!await confirm({ title: t('confirmDialog.deleteTitle'), message: t('adminServices.deleteConfirm', { name: service.name }), confirmLabel: t('confirmDialog.delete') })) return
    router.delete(`/admin/services/${service.id}`, { preserveScroll: true })
}
</script>

<template>
    <Head :title="$t('adminServices.metaTitle')" />
    <AppLayout>
        <main class="admin-users-page admin-services-page">
            <section class="admin-users-hero">
                <div><span>{{ $t('adminServices.eyebrow') }}</span><h1>{{ $t('adminServices.title') }}</h1><p>{{ $t('adminServices.subtitle') }}</p></div>
                <Link href="/admin/services/create" class="admin-users-primary"><i class="fas fa-plus"></i>{{ $t('adminServices.add') }}</Link>
            </section>

            <section class="admin-users-card">
                <form class="admin-users-filters admin-services-filters" @submit.prevent="applyFilters">
                    <label><span class="sr-only">{{ $t('adminServices.search') }}</span><i class="fas fa-search"></i><input v-model.trim="filters.search" type="search" :placeholder="$t('adminServices.searchPlaceholder')"></label>
                    <select v-model="filters.category" :aria-label="$t('adminServices.category')"><option value="">{{ $t('adminServices.allCategories') }}</option><option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option></select>
                    <select v-model="filters.status" :aria-label="$t('adminServices.status')"><option value="">{{ $t('adminServices.allStatuses') }}</option><option value="active">{{ $t('adminServices.active') }}</option><option value="inactive">{{ $t('adminServices.inactive') }}</option></select>
                    <button type="submit">{{ $t('adminServices.filter') }}</button>
                    <button type="button" class="is-ghost" @click="resetFilters">{{ $t('adminServices.reset') }}</button>
                </form>

                <p v-if="page.props?.errors?.service" class="admin-users-error" role="alert">{{ page.props.errors.service }}</p>

                <div v-if="services.data.length" class="admin-users-table-wrap">
                    <table class="admin-users-table admin-services-table">
                        <thead><tr><th>{{ $t('adminServices.service') }}</th><th>{{ $t('adminServices.provider') }}</th><th>{{ $t('adminServices.location') }}</th><th>{{ $t('adminServices.prices') }}</th><th>{{ $t('adminServices.status') }}</th><th><span class="sr-only">{{ $t('adminServices.actions') }}</span></th></tr></thead>
                        <tbody>
                            <tr v-for="service in services.data" :key="service.id">
                                <td><span class="admin-service-table-icon"><i :class="service.icon || 'fas fa-screwdriver-wrench'"></i></span><span><strong>{{ service.name }}</strong><small>{{ service.category?.name || $t('adminServices.uncategorized') }}</small></span></td>
                                <td><strong>{{ providerName(service) || $t('adminServices.unknownProvider') }}</strong></td>
                                <td><strong>{{ service.city?.name || $t('adminServices.notSpecified') }}</strong></td>
                                <td><strong>{{ formatPrice(service.price_min) }}</strong><small>{{ $t('adminServices.toPrice', { price: formatPrice(service.price_max) }) }}</small></td>
                                <td><span :class="['admin-users-status', service.is_active ? 'is-verified' : 'is-pending']">{{ service.is_active ? $t('adminServices.active') : $t('adminServices.inactive') }}</span></td>
                                <td><div class="admin-users-actions"><Link :href="`/admin/services/${service.id}/edit`" :aria-label="$t('adminServices.edit')"><i class="fas fa-pen"></i></Link><button type="button" :aria-label="$t('adminServices.delete')" @click="removeService(service)"><i class="fas fa-trash"></i></button></div></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p v-else class="admin-users-empty">{{ $t('adminServices.empty') }}</p>
                <nav v-if="services.links?.length > 3" class="admin-users-pagination" :aria-label="$t('adminServices.pagination')"><Link v-for="link in services.links" :key="link.label" :href="link.url || ''" :class="{ active: link.active, disabled: !link.url }" preserve-scroll v-html="link.label" /></nav>
            </section>
        </main>
    </AppLayout>
</template>

<style lang="scss" src="../../../../scss/pages/admin/_services.scss"></style>
