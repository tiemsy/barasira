<script setup>
import { computed, ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'
import DashboardIcon from '@/Components/DashboardIcon.vue'

const props = defineProps({
    documents: { type: Array, default: () => [] },
    swaggerUrl: { type: String, required: true },
})

const { locale, t } = useI18n()
const selectedKey = ref(props.documents.find(document => document.available)?.key || null)
const selectedDocument = computed(() => props.documents.find(document => document.key === selectedKey.value))

const documentTitle = document => t(`adminDocumentation.documents.${document.key}.title`, document.title)
const documentDescription = document => t(`adminDocumentation.documents.${document.key}.description`, document.description)
const documentIcon = document => document.type === 'pdf' ? 'file-check' : document.type === 'code' ? 'code' : 'list'
const formatSize = bytes => bytes === null
    ? '—'
    : new Intl.NumberFormat(locale.value, { maximumFractionDigits: 1 }).format(bytes / 1024) + ' KB'
const formatDate = timestamp => timestamp
    ? new Intl.DateTimeFormat(locale.value, { dateStyle: 'medium' }).format(timestamp * 1000)
    : '—'
</script>

<template>
    <Head :title="$t('adminDocumentation.metaTitle')" />
    <AppLayout>
        <main class="admin-documentation">
            <section class="admin-documentation__hero">
                <div>
                    <span class="admin-documentation__eyebrow">
                        <DashboardIcon name="shield" />{{ $t('adminDocumentation.eyebrow') }}
                    </span>
                    <h1>{{ $t('adminDocumentation.title') }}</h1>
                    <p>{{ $t('adminDocumentation.subtitle') }}</p>
                </div>
                <Link href="/admin/dashboard" class="admin-documentation__back">
                    <DashboardIcon name="arrow-left" />{{ $t('adminDocumentation.back') }}
                </Link>
            </section>

            <section class="admin-documentation__swagger">
                <span class="admin-documentation__swagger-icon"><DashboardIcon name="code" /></span>
                <div>
                    <strong>{{ $t('adminDocumentation.swaggerTitle') }}</strong>
                    <p>{{ $t('adminDocumentation.swaggerDescription') }}</p>
                </div>
                <a :href="swaggerUrl" target="_blank" rel="noopener noreferrer">
                    {{ $t('adminDocumentation.openSwagger') }}<DashboardIcon name="external" />
                </a>
            </section>

            <section class="admin-documentation__workspace">
                <aside class="admin-documentation__catalog" :aria-label="$t('adminDocumentation.catalog')">
                    <button
                        v-for="document in documents"
                        :key="document.key"
                        type="button"
                        :disabled="!document.available"
                        :class="{ active: document.key === selectedKey }"
                        @click="selectedKey = document.key"
                    >
                        <span class="admin-documentation__document-icon"><DashboardIcon :name="documentIcon(document)" /></span>
                        <span>
                            <strong>{{ documentTitle(document) }}</strong>
                            <small>{{ document.available ? formatSize(document.size) : $t('adminDocumentation.unavailable') }}</small>
                        </span>
                        <DashboardIcon name="chevron" />
                    </button>
                </aside>

                <article v-if="selectedDocument" class="admin-documentation__viewer">
                    <header>
                        <div>
                            <span>{{ $t('adminDocumentation.selectedDocument') }}</span>
                            <h2>{{ documentTitle(selectedDocument) }}</h2>
                            <p>{{ documentDescription(selectedDocument) }}</p>
                        </div>
                        <div class="admin-documentation__actions">
                            <a :href="selectedDocument.view_url" target="_blank" rel="noopener noreferrer">
                                <DashboardIcon name="external" />{{ $t('adminDocumentation.open') }}
                            </a>
                            <a :href="selectedDocument.download_url" class="primary">
                                <DashboardIcon name="arrow" />{{ $t('adminDocumentation.download') }}
                            </a>
                        </div>
                    </header>
                    <div class="admin-documentation__meta">
                        <span><DashboardIcon name="storage" />{{ formatSize(selectedDocument.size) }}</span>
                        <span><DashboardIcon name="clock" />{{ $t('adminDocumentation.updated', { date: formatDate(selectedDocument.updated_at) }) }}</span>
                    </div>
                    <iframe
                        :key="selectedDocument.key"
                        :src="selectedDocument.view_url"
                        :title="documentTitle(selectedDocument)"
                    />
                </article>

                <div v-else class="admin-documentation__empty">
                    <DashboardIcon name="warning" />
                    <h2>{{ $t('adminDocumentation.emptyTitle') }}</h2>
                    <p>{{ $t('adminDocumentation.emptyDescription') }}</p>
                </div>
            </section>

            <p class="admin-documentation__notice">
                <DashboardIcon name="lock" />{{ $t('adminDocumentation.notice') }}
            </p>
        </main>
    </AppLayout>
</template>

<style lang="scss" src="../../../../scss/pages/admin/_documentation.scss"></style>
