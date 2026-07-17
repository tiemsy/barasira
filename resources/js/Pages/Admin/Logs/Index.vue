<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    sources: { type: Array, default: () => [] },
    selectedSource: { type: String, default: 'laravel' },
    selectedLines: { type: Number, default: 200 },
    entries: { type: Array, default: () => [] },
})

const { locale, t } = useI18n()
const source = ref(props.selectedSource)
const lines = ref(props.selectedLines)
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
</script>

<template>
    <Head :title="$t('adminLogs.metaTitle')" />
    <AppLayout>
        <main class="admin-logs">
            <section class="admin-logs__hero">
                <div>
                    <span class="admin-logs__eyebrow"><i class="fas fa-shield-halved"></i>{{ $t('adminLogs.eyebrow') }}</span>
                    <h1>{{ $t('adminLogs.title') }}</h1>
                    <p>{{ $t('adminLogs.subtitle') }}</p>
                </div>
                <Link href="/admin/dashboard" class="admin-logs__back"><i class="fas fa-arrow-left"></i>{{ $t('adminLogs.back') }}</Link>
            </section>

            <section class="admin-logs__workspace">
                <aside class="admin-logs__sources" :aria-label="$t('adminLogs.sourceList')">
                    <button v-for="item in sources" :key="item.key" type="button" :class="{ active: item.key === source }" @click="selectSource(item.key)">
                        <span class="admin-logs__source-icon"><i :class="item.key.startsWith('nginx') ? 'fas fa-server' : item.key === 'php' ? 'fab fa-php' : 'fab fa-laravel'"></i></span>
                        <span><strong>{{ sourceLabel(item.key) }}</strong><small>{{ item.available ? $t('adminLogs.available') : $t('adminLogs.unavailable') }}</small></span>
                        <i class="fas fa-circle admin-logs__status" :class="item.available ? 'is-ready' : 'is-offline'"></i>
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
                            <button type="button" @click="load"><i class="fas fa-rotate"></i>{{ $t('adminLogs.refresh') }}</button>
                        </div>
                    </header>

                    <div v-if="currentSource?.available" class="admin-logs__meta">
                        <span><i class="fas fa-hard-drive"></i>{{ formatSize(currentSource.size) }}</span>
                        <span><i class="fas fa-clock"></i>{{ $t('adminLogs.updated', { date: formatDate(currentSource.updated_at) }) }}</span>
                        <span><i class="fas fa-list-ol"></i>{{ $t('adminLogs.displayed', { count: entries.length }) }}</span>
                    </div>

                    <div v-if="!currentSource?.available" class="admin-logs__state">
                        <i class="fas fa-plug-circle-xmark"></i><h3>{{ $t('adminLogs.unavailableTitle') }}</h3><p>{{ $t('adminLogs.unavailableHint') }}</p>
                    </div>
                    <div v-else-if="!entries.length" class="admin-logs__state">
                        <i class="fas fa-file-circle-check"></i><h3>{{ $t('adminLogs.emptyTitle') }}</h3><p>{{ $t('adminLogs.emptyHint') }}</p>
                    </div>
                    <div v-else class="admin-logs__console" role="log" :aria-label="$t('adminLogs.console')">
                        <div v-for="entry in entries" :key="`${entry.number}-${entry.content}`" class="admin-logs__line"><span>{{ entry.number }}</span><code>{{ entry.content }}</code></div>
                    </div>
                </div>
            </section>
            <p class="admin-logs__notice"><i class="fas fa-lock"></i>{{ $t('adminLogs.notice') }}</p>
        </main>
    </AppLayout>
</template>

<style lang="scss" src="../../../../scss/pages/admin/_logs.scss"></style>
