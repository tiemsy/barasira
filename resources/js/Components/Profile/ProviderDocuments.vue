<script setup>
import { useForm, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useConfirmDialog } from '@/composables/useConfirmDialog'
import DashboardIcon from '@/Components/DashboardIcon.vue'

defineProps({ documents: { type: Array, default: () => [] } })
const { t } = useI18n()
const { confirm } = useConfirmDialog()
const form = useForm({ document_type: 'identity', label: '', file: null })
const maximumFileSize = 10 * 1024 * 1024
const types = ['identity', 'diploma', 'certification', 'address_proof', 'other']
const formatSize = bytes => bytes ? `${(bytes / 1024 / 1024).toFixed(1)} Mo` : ''
function submit() {
    if (!form.file || form.file.size > maximumFileSize) {
        form.setError('file', t('providerDocuments.fileTooLarge'))
        return
    }
    form.post('/profile/documents', { preserveScroll: true, forceFormData: true, onSuccess: () => form.reset('label', 'file') })
}
function chooseFile(event) {
    const file = event.target.files?.[0] ?? null
    form.clearErrors('file')
    if (file && file.size > maximumFileSize) {
        form.file = null
        event.target.value = ''
        form.setError('file', t('providerDocuments.fileTooLarge'))
        return
    }
    form.file = file
}
async function remove(document) {
    if (!await confirm({ title: t('providerDocuments.deleteTitle'), message: t('providerDocuments.deleteText'), confirmLabel: t('providerDocuments.delete'), tone: 'danger' })) return
    router.delete(`/profile/documents/${document.id}`, { preserveScroll: true })
}
</script>

<template>
    <section class="provider-documents"><header><span>{{ $t('providerDocuments.eyebrow') }}</span><h2>{{ $t('providerDocuments.title') }}</h2><p>{{ $t('providerDocuments.hint') }}</p></header>
        <aside class="identity-badge-info"><DashboardIcon name="verified" /><p><strong>{{ $t('providerDocuments.badgeInfoTitle') }}</strong><span>{{ $t('providerDocuments.badgeInfoText') }}</span></p></aside>
        <form @submit.prevent="submit"><label><span>{{ $t('providerDocuments.type') }}</span><select v-model="form.document_type"><option v-for="type in types" :key="type" :value="type">{{ $t(`providerDocuments.types.${type}`) }}</option></select><small v-if="form.errors.document_type">{{ form.errors.document_type }}</small></label><label v-if="form.document_type === 'other'"><span>{{ $t('providerDocuments.label') }}</span><input v-model.trim="form.label" required maxlength="150"><small v-if="form.errors.label">{{ form.errors.label }}</small></label><label class="document-file"><span>{{ $t('providerDocuments.file') }}</span><input type="file" accept=".pdf,.jpg,.jpeg,.png,.webp" required :aria-invalid="Boolean(form.errors.file)" @change="chooseFile"><small>{{ $t('providerDocuments.fileHint') }}</small><small v-if="form.errors.file" class="error" role="alert">{{ form.errors.file }}</small></label><button :disabled="form.processing">{{ form.processing ? $t('providerDocuments.uploading') : $t('providerDocuments.upload') }}</button></form>
        <div v-if="documents.length" class="document-list"><article v-for="document in documents" :key="document.id"><div><strong>{{ document.label || $t(`providerDocuments.types.${document.document_type}`) }}</strong><small>{{ document.original_name }} · {{ formatSize(document.file_size) }}</small><p v-if="document.review_comment">{{ document.review_comment }}</p></div><span :class="`status-${document.status}`">{{ $t(`providerDocuments.statuses.${document.status}`) }}</span><div><a :href="`/profile/documents/${document.id}`">{{ $t('providerDocuments.download') }}</a><button type="button" @click="remove(document)">{{ $t('providerDocuments.delete') }}</button></div></article></div><p v-else class="documents-empty">{{ $t('providerDocuments.empty') }}</p>
    </section>
</template>

<style scoped>
.provider-documents{margin-top:1.5rem;padding:1.5rem;border:1px solid #dce8e1;border-radius:18px;background:#fff}.provider-documents>header>span{color:#177245;font-size:.75rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase}.provider-documents h2{margin:.35rem 0}.provider-documents header p{margin:0;color:#68776e}.identity-badge-info{display:flex;align-items:flex-start;gap:.75rem;margin-top:1rem;padding:1rem;border:1px solid #cce4d6;border-radius:12px;background:#eff9f3;color:#145d39}.identity-badge-info svg{flex:0 0 24px;width:24px;height:24px}.identity-badge-info p{display:grid;gap:.2rem;margin:0}.identity-badge-info span{color:#4e6759;font-size:.9rem;line-height:1.5}.provider-documents form{display:grid;grid-template-columns:1fr 1fr 1.4fr auto;align-items:end;gap:1rem;margin-top:1.3rem;padding:1rem;border-radius:12px;background:#f5f9f6}.provider-documents label{display:grid;gap:.35rem;font-weight:700}.provider-documents input,.provider-documents select{box-sizing:border-box;width:100%;padding:.7rem;border:1px solid #cbd8d0;border-radius:8px;background:#fff}.provider-documents small{color:#718078;font-weight:500}.provider-documents small.error,.provider-documents label>small:last-child:not(:only-child){color:#b42318}.provider-documents form>button{min-height:42px;padding:.7rem 1rem;border:0;border-radius:8px;background:#177245;color:#fff;font-weight:800;cursor:pointer}.provider-documents form>button:disabled{opacity:.6}.document-list{display:grid;gap:.65rem;margin-top:1rem}.document-list article{display:grid;grid-template-columns:1fr auto auto;align-items:center;gap:1rem;padding:.9rem;border:1px solid #e4ebe7;border-radius:10px}.document-list strong,.document-list small{display:block}.document-list p{margin:.3rem 0 0;color:#a13b2e;font-size:.85rem}.document-list>article>span{padding:.3rem .55rem;border-radius:999px;font-size:.72rem;font-weight:800}.status-en_attente{background:#fff3d6;color:#946411}.status-valide{background:#e5f5ec;color:#146c43}.status-rejete{background:#feeceb;color:#b42318}.document-list article>div:last-child{display:flex;gap:.5rem}.document-list a,.document-list button{padding:.45rem .6rem;border:0;border-radius:7px;background:#e8f1ec;color:#176b45;font:inherit;font-size:.8rem;font-weight:700;text-decoration:none;cursor:pointer}.document-list button{background:#feeceb;color:#b42318}.documents-empty{color:#718078}@media(max-width:850px){.provider-documents form{grid-template-columns:1fr 1fr}.document-list article{grid-template-columns:1fr auto}.document-list article>div:last-child{grid-column:1/-1}}@media(max-width:560px){.provider-documents form,.document-list article{grid-template-columns:1fr}}
</style>
