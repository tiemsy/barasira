<script setup>
import { reactive } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'
import UserActionIcon from '@/Components/Admin/UserActionIcon.vue'
import DashboardIcon from '@/Components/DashboardIcon.vue'
import { useConfirmDialog } from '@/composables/useConfirmDialog'

const props = defineProps({
    users: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
})
const page = usePage()
const { t } = useI18n()
const { confirm } = useConfirmDialog()
const filters = reactive({ search: props.filters.search ?? '', role: props.filters.role ?? '' })

function applyFilters() {
    router.get('/admin/users', filters, { preserveState: true, replace: true })
}

function resetFilters() {
    filters.search = ''
    filters.role = ''
    applyFilters()
}

async function removeUser(user) {
    if (!await confirm({ title: t('confirmDialog.deleteTitle'), message: t('adminUsers.deleteConfirm', { name: fullName(user) }), confirmLabel: t('confirmDialog.delete') })) return
    router.delete(`/admin/users/${user.id}`, { preserveScroll: true })
}

async function impersonate(user) {
    if (!await confirm({ title: t('confirmDialog.continueTitle'), message: t('impersonation.confirm', { name: fullName(user) }), confirmLabel: t('confirmDialog.continue'), tone: 'warning' })) return
    router.post(`/admin/users/${user.id}/impersonate`)
}

const fullName = user => `${user.first_name ?? ''} ${user.last_name ?? ''}`.trim()
</script>

<template>
    <Head :title="$t('adminUsers.metaTitle')" />
    <AppLayout>
        <main class="admin-users-page">
            <section class="admin-users-hero">
                <div>
                    <span>{{ $t('adminUsers.eyebrow') }}</span>
                    <h1>{{ $t('adminUsers.title') }}</h1>
                    <p>{{ $t('adminUsers.subtitle') }}</p>
                </div>
                <Link href="/admin/users/create" class="admin-users-primary"><DashboardIcon name="user-plus" />{{ $t('adminUsers.add') }}</Link>
            </section>

            <section class="admin-users-card">
                <form class="admin-users-filters" @submit.prevent="applyFilters">
                    <label>
                        <span class="sr-only">{{ $t('adminUsers.search') }}</span>
                        <DashboardIcon name="search" />
                        <input v-model.trim="filters.search" type="search" :placeholder="$t('adminUsers.searchPlaceholder')">
                    </label>
                    <select v-model="filters.role" :aria-label="$t('adminUsers.filterRole')">
                        <option value="">{{ $t('adminUsers.allRoles') }}</option>
                        <option value="client">{{ $t('navbar.roles.client') }}</option>
                        <option value="prestataire">{{ $t('navbar.roles.provider') }}</option>
                        <option value="admin">{{ $t('navbar.roles.admin') }}</option>
                        <option value="superadmin">{{ $t('navbar.roles.superadmin') }}</option>
                    </select>
                    <button type="submit">{{ $t('adminUsers.filter') }}</button>
                    <button type="button" class="is-ghost" @click="resetFilters">{{ $t('adminUsers.reset') }}</button>
                </form>

                <p v-if="page.props?.errors?.user" class="admin-users-error" role="alert">{{ page.props.errors.user }}</p>

                <div v-if="users.data.length" class="admin-users-table-wrap">
                    <table class="admin-users-table">
                        <thead><tr><th>{{ $t('adminUsers.user') }}</th><th>{{ $t('adminUsers.contact') }}</th><th>{{ $t('adminUsers.role') }}</th><th>{{ $t('adminUsers.status') }}</th><th><span class="sr-only">{{ $t('adminUsers.actions') }}</span></th></tr></thead>
                        <tbody>
                            <tr v-for="user in users.data" :key="user.id">
                                <td><span class="admin-users-avatar"><img v-if="user.avatar_url" :src="user.avatar_url" alt=""><b v-else>{{ fullName(user).charAt(0) }}</b></span><span><strong>{{ fullName(user) }}</strong><small>#{{ user.id }}</small></span></td>
                                <td><strong>{{ user.email }}</strong><small>{{ user.phone || $t('adminUsers.notSpecified') }}</small></td>
                                <td><span class="admin-users-role">{{ $t(`navbar.roles.${user.role === 'prestataire' ? 'provider' : user.role}`) }}</span></td>
                                <td><span :class="['admin-users-status', user.verified ? 'is-verified' : 'is-pending']">{{ user.verified ? $t('adminUsers.verified') : $t('adminUsers.unverified') }}</span></td>
                                <td><div v-if="user.role !== 'superadmin' || page.props?.auth?.user?.role === 'superadmin'" class="admin-users-actions"><button v-if="page.props?.auth?.user?.role === 'superadmin' && user.id !== page.props.auth.user.id" type="button" class="is-impersonate" :title="$t('impersonation.start')" :aria-label="$t('impersonation.start')" @click="impersonate(user)"><UserActionIcon name="impersonate" /></button><Link :href="`/admin/users/${user.id}/edit`" class="is-edit" :aria-label="$t('adminUsers.edit')"><UserActionIcon name="edit" /></Link><button type="button" class="is-delete" :aria-label="$t('adminUsers.delete')" @click="removeUser(user)"><UserActionIcon name="delete" /></button></div></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p v-else class="admin-users-empty">{{ $t('adminUsers.empty') }}</p>

                <nav v-if="users.links?.length > 3" class="admin-users-pagination" :aria-label="$t('adminUsers.pagination')">
                    <Link v-for="link in users.links" :key="link.label" :href="link.url || ''" :class="{ active: link.active, disabled: !link.url }" preserve-scroll v-html="link.label" />
                </nav>
            </section>
        </main>
    </AppLayout>
</template>

<style lang="scss" src="../../../../scss/pages/admin/_users.scss"></style>
