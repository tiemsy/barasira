<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'
import DashboardIcon from '@/Components/DashboardIcon.vue'

const props = defineProps({
    sources: { type: Array, default: () => [] },
    selectedSource: { type: String, default: 'laravel' },
    selectedLines: { type: Number, default: 200 },
    entries: { type: Array, default: () => [] },
})

const { locale, t } = useI18n()
const source = ref(props.selectedSource)
const lines = ref(props.selectedLines)
const dateFrom = ref('')
const dateTo = ref('')
const currentSource = computed(() => props.sources.find(item => item.key === source.value))

const sourceLabel = key => t(`adminLogs.sources.${key}`)
const formatSize = bytes => bytes === null ? '—' : new Intl.NumberFormat(locale.value, { maximumFractionDigits: 1 }).format(bytes / 1024) + ' KB'
const formatDate = timestamp => timestamp ? new Intl.DateTimeFormat(locale.value, { dateStyle: 'medium', timeStyle: 'medium' }).format(timestamp * 1000) : '—'

function load() {
    router.get('/admin/logs', { source: source.value, lines: lines.value }, { preserveScroll: true, replace: true })
}

function selectSource(key) {
    source.value = key
    load()
}

function purge(mode) {
    if (mode === 'period' && (!dateFrom.value || !dateTo.value)) return
    if (!window.confirm(t(mode === 'all' ? 'adminLogs.purgeAllConfirm' : 'adminLogs.purgePeriodConfirm'))) return

    router.visit('/admin/logs', {
        method: 'delete',
        data: { mode, date_from: dateFrom.value || null, date_to: dateTo.value || null },
        preserveScroll: true,
    })
}
</script>

<template>
    <Head :title="$t('adminLogs.metaTitle')" />
    <AppLayout>
        <main class="admin-logs">
            <section class="admin-logs__hero">
                <div>
                    <span class="admin-logs__eyebrow"><DashboardIcon name="shield" />{{ $t('adminLogs.eyebrow') }}</span>
                    <h1>{{ $t('adminLogs.title') }}</h1>
                    <p>{{ $t('adminLogs.subtitle') }}</p>
                </div>
                <Link href="/admin/dashboard" class="admin-logs__back"><DashboardIcon name="arrow-left" />{{ $t('adminLogs.back') }}</Link>
            </section>

            <section class="admin-logs__workspace">
                <aside class="admin-logs__sources" :aria-label="$t('adminLogs.sourceList')">
                    <button v-for="item in sources" :key="item.key" type="button" :class="{ active: item.key === source }" @click="selectSource(item.key)">
                        <span class="admin-logs__source-icon"><DashboardIcon :name="item.key === 'audit' ? 'shield' : item.key.startsWith('nginx') ? 'server' : item.key === 'php' ? 'code' : 'framework'" /></span>
                        <span><strong>{{ sourceLabel(item.key) }}</strong><small>{{ item.available ? $t('adminLogs.available') : $t('adminLogs.unavailable') }}</small></span>
                        <DashboardIcon name="status" class="admin-logs__status" :class="item.available ? 'is-ready' : 'is-offline'" />
                    </button>
                </aside>

                <div class="admin-logs__viewer">
                    <header class="admin-logs__toolbar">
                        <div>
                            <span>{{ $t('adminLogs.currentSource') }}</span>
                            <h2>{{ sourceLabel(source) }}</h2>
                        </div>
                        <div class="admin-logs__controls">
                            <label>{{ $t('adminLogs.lines') }}
                                <select v-model.number="lines" @change="load"><option v-for="count in [50, 100, 200, 500]" :key="count" :value="count">{{ count }}</option></select>
                            </label>
                            <button type="button" @click="load"><DashboardIcon name="refresh" />{{ $t('adminLogs.refresh') }}</button>
                        </div>
                    </header>

                    <div v-if="currentSource?.available" class="admin-logs__meta">
                        <span><DashboardIcon name="storage" />{{ formatSize(currentSource.size) }}</span>
                        <span><DashboardIcon name="clock" />{{ $t('adminLogs.updated', { date: formatDate(currentSource.updated_at) }) }}</span>
                        <span><DashboardIcon name="ordered-list" />{{ $t('adminLogs.displayed', { count: entries.length }) }}</span>
                    </div>

                    <section class="admin-logs__purge">
                        <div><strong>{{ $t('adminLogs.purgeTitle') }}</strong><small>{{ $t('adminLogs.purgeHint') }}</small></div>
                        <label>{{ $t('adminLogs.dateFrom') }}<input v-model="dateFrom" type="date" /></label>
                        <label>{{ $t('adminLogs.dateTo') }}<input v-model="dateTo" type="date" /></label>
                        <button type="button" :disabled="!dateFrom || !dateTo" @click="purge('period')">{{ $t('adminLogs.purgePeriod') }}</button>
                        <button type="button" class="danger" @click="purge('all')">{{ $t('adminLogs.purgeAll') }}</button>
                    </section>

                    <div v-if="!currentSource?.available" class="admin-logs__state">
                        <DashboardIcon name="disconnected" /><h3>{{ $t('adminLogs.unavailableTitle') }}</h3><p>{{ $t('adminLogs.unavailableHint') }}</p>
                    </div>
                    <div v-else-if="!entries.length" class="admin-logs__state">
                        <DashboardIcon name="file-check" /><h3>{{ $t('adminLogs.emptyTitle') }}</h3><p>{{ $t('adminLogs.emptyHint') }}</p>
                    </div>
                    <div v-else class="admin-logs__console" role="log" :aria-label="$t('adminLogs.console')">
                        <div v-for="entry in entries" :key="`${entry.number}-${entry.content}`" class="admin-logs__line"><span>{{ entry.number }}</span><code>{{ entry.content }}</code></div>
                    </div>
                </div>
            </section>
            <p class="admin-logs__notice"><DashboardIcon name="lock" />{{ $t('adminLogs.notice') }}</p>
        </main>
    </AppLayout>
</template>

<style lang="scss" src="../../../../scss/pages/admin/_logs.scss"></style>
