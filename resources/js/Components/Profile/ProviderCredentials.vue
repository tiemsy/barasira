<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useConfirmDialog } from '@/composables/useConfirmDialog'
import DashboardIcon from '@/Components/DashboardIcon.vue'

const props = defineProps({ resume: { type: Object, default: null } })
const { t } = useI18n()
const { confirm } = useConfirmDialog()
const modalOpen = ref(false)
const editingId = ref(null)
const activeType = ref('education')
const saving = ref(false)
const errors = ref({})
const form = ref({})

const definitions = {
    education: {
        collection: 'educations', endpoint: 'educations', icon: 'education',
        fields: ['school_name', 'degree', 'field', 'start_year', 'end_year', 'city', 'country'],
        required: ['school_name', 'degree', 'field'],
    },
    experience: {
        collection: 'experiences', endpoint: 'experiences', icon: 'experience',
        fields: ['company_name', 'position', 'start_date', 'end_date', 'description'],
        required: ['company_name', 'position', 'start_date'],
    },
    certification: {
        collection: 'certifications', endpoint: 'certifications', icon: 'certificate',
        fields: ['name', 'issuer', 'issue_date', 'expiration_date', 'credential_url'],
        required: ['name', 'issuer', 'issue_date'],
    },
}

const activeDefinition = computed(() => definitions[activeType.value])
const items = type => props.resume?.[definitions[type].collection] ?? []
const fieldType = field => field.includes('date') ? 'date' : field.includes('year') ? 'number' : field === 'credential_url' ? 'url' : 'text'
const isTextarea = field => field === 'description'
const emptyForm = type => Object.fromEntries(definitions[type].fields.map(field => [field, '']))

function openCreate(type) {
    activeType.value = type
    editingId.value = null
    form.value = emptyForm(type)
    errors.value = {}
    modalOpen.value = true
}

function openEdit(type, item) {
    activeType.value = type
    editingId.value = item.id
    form.value = Object.fromEntries(definitions[type].fields.map(field => [field, item[field] ?? '']))
    errors.value = {}
    modalOpen.value = true
}

function closeModal() {
    if (!saving.value) modalOpen.value = false
}

function submit() {
    const endpoint = activeDefinition.value.endpoint
    const url = editingId.value ? `/profile/${endpoint}/${editingId.value}` : `/profile/${endpoint}`
    const method = editingId.value ? 'put' : 'post'
    saving.value = true
    errors.value = {}
    router[method](url, form.value, {
        preserveScroll: true,
        onError: response => { errors.value = response },
        onSuccess: () => { modalOpen.value = false },
        onFinish: () => { saving.value = false },
    })
}

async function remove(type, item) {
    if (!await confirm({
        title: t('providerCredentials.deleteTitle'),
        message: t('providerCredentials.deleteMessage'),
        confirmLabel: t('providerCredentials.delete'),
        tone: 'danger',
    })) return

    router.delete(`/profile/${definitions[type].endpoint}/${item.id}`, { preserveScroll: true })
}

function summary(type, item) {
    if (type === 'education') return `${item.degree} · ${item.school_name}`
    if (type === 'experience') return `${item.position} · ${item.company_name}`
    return `${item.name} · ${item.issuer}`
}

function period(type, item) {
    if (type === 'education') return [item.start_year, item.end_year].filter(Boolean).join(' – ')
    if (type === 'experience') return [item.start_date, item.end_date || t('providerCredentials.current')].filter(Boolean).join(' – ')
    return item.issue_date
}
</script>

<template>
    <section class="credentials-section">
        <header class="credentials-heading">
            <span>{{ $t('providerCredentials.eyebrow') }}</span>
            <h2>{{ $t('providerCredentials.title') }}</h2>
            <p>{{ $t('providerCredentials.hint') }}</p>
        </header>

        <div class="credentials-grid">
            <article v-for="type in ['education', 'experience', 'certification']" :key="type" class="credential-card">
                <header>
                    <span class="credential-card__icon"><DashboardIcon :name="definitions[type].icon" /></span>
                    <div><h3>{{ $t(`providerCredentials.${type}.title`) }}</h3><small>{{ items(type).length }}</small></div>
                    <button type="button" @click="openCreate(type)"><DashboardIcon name="plus" />{{ $t('providerCredentials.add') }}</button>
                </header>
                <div v-if="items(type).length" class="credential-list">
                    <div v-for="item in items(type)" :key="item.id" class="credential-item">
                        <div><strong>{{ summary(type, item) }}</strong><small>{{ period(type, item) }}</small><p v-if="type === 'education' && item.field">{{ item.field }}</p><p v-if="type === 'experience' && item.description">{{ item.description }}</p></div>
                        <div class="credential-item__actions">
                            <button type="button" :aria-label="$t('providerCredentials.edit')" @click="openEdit(type, item)"><DashboardIcon name="edit" /></button>
                            <button type="button" class="danger" :aria-label="$t('providerCredentials.delete')" @click="remove(type, item)"><DashboardIcon name="delete" /></button>
                        </div>
                    </div>
                </div>
                <p v-else class="credential-empty">{{ $t(`providerCredentials.${type}.empty`) }}</p>
            </article>
        </div>

        <Teleport to="body">
            <div v-if="modalOpen" class="credential-modal-backdrop" @mousedown.self="closeModal">
                <section class="credential-modal" role="dialog" aria-modal="true" aria-labelledby="credential-modal-title">
                    <button type="button" class="credential-modal__close" :disabled="saving" @click="closeModal">×</button>
                    <span class="credential-modal__icon"><DashboardIcon :name="activeDefinition.icon" /></span>
                    <h2 id="credential-modal-title">{{ $t(`providerCredentials.${activeType}.${editingId ? 'edit' : 'add'}`) }}</h2>
                    <form @submit.prevent="submit">
                        <label v-for="field in activeDefinition.fields" :key="field" :class="{ wide: isTextarea(field) }">
                            <span>{{ $t(`providerCredentials.fields.${field}`) }}<b v-if="activeDefinition.required.includes(field)">*</b></span>
                            <textarea v-if="isTextarea(field)" v-model="form[field]" rows="4" maxlength="2000"></textarea>
                            <input v-else v-model="form[field]" :type="fieldType(field)" :required="activeDefinition.required.includes(field)" :min="field.includes('year') ? 1950 : undefined" :max="field.includes('year') ? new Date().getFullYear() + 10 : undefined">
                            <small v-if="errors[field]" class="field-error">{{ errors[field] }}</small>
                        </label>
                        <div class="credential-modal__actions">
                            <button type="button" class="secondary" :disabled="saving" @click="closeModal">{{ $t('profile.cancel') }}</button>
                            <button type="submit" :disabled="saving">{{ saving ? $t('profile.saving') : $t('profile.save') }}</button>
                        </div>
                    </form>
                </section>
            </div>
        </Teleport>
    </section>
</template>

<style scoped lang="scss">
.credentials-section{margin-top:1.5rem}.credentials-heading>span{color:#177245;font-size:.75rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase}.credentials-heading h2{margin:.35rem 0}.credentials-heading p{margin:0;color:#68776e}.credentials-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1rem;margin-top:1.2rem}.credential-card{padding:1.1rem;border:1px solid #dce8e1;border-radius:16px;background:#fff;box-shadow:0 7px 22px rgba(16,55,33,.05)}.credential-card>header{display:grid;grid-template-columns:auto 1fr auto;align-items:center;gap:.65rem}.credential-card__icon,.credential-modal__icon{display:grid;width:38px;height:38px;place-items:center;border-radius:11px;background:#e8f5ed;color:#177245}.credential-card h3{margin:0;font-size:1rem}.credential-card header small{color:#78857e}.credential-card header button{display:flex;align-items:center;gap:.3rem;padding:.55rem .65rem;border:0;border-radius:8px;background:#177245;color:#fff;font-weight:750;cursor:pointer}.credential-list{display:grid;gap:.65rem;margin-top:1rem}.credential-item{display:flex;align-items:flex-start;justify-content:space-between;gap:.5rem;padding:.75rem;border-radius:10px;background:#f7faf8}.credential-item strong,.credential-item small{display:block}.credential-item small{margin-top:.2rem;color:#728078}.credential-item p{margin:.35rem 0 0;color:#59675f;font-size:.85rem}.credential-item__actions{display:flex;gap:.25rem}.credential-item__actions button{width:30px;height:30px;border:0;border-radius:7px;background:#e8f1ec;color:#275c3e;cursor:pointer}.credential-item__actions button.danger{background:#feeceb;color:#b42318}.credential-empty{margin:1rem 0 0;color:#7b8780;font-size:.9rem}.credential-modal-backdrop{display:grid;position:fixed;z-index:3200;inset:0;padding:1rem;place-items:center;overflow:auto;background:rgba(8,25,15,.68);backdrop-filter:blur(5px)}.credential-modal{position:relative;width:min(680px,100%);box-sizing:border-box;padding:1.6rem;border-radius:20px;background:#fff;box-shadow:0 28px 80px rgba(0,0,0,.3)}.credential-modal__close{position:absolute;top:.8rem;right:.8rem;width:34px;height:34px;border:0;border-radius:50%;font-size:1.3rem;cursor:pointer}.credential-modal h2{margin:.8rem 0 1.2rem}.credential-modal form{display:grid;grid-template-columns:1fr 1fr;gap:1rem}.credential-modal label>span{display:block;margin-bottom:.4rem;font-weight:750}.credential-modal label b{color:#b42318}.credential-modal input,.credential-modal textarea{width:100%;box-sizing:border-box;padding:.75rem;border:1px solid #ccd8d1;border-radius:9px;font:inherit}.credential-modal label.wide,.credential-modal__actions{grid-column:1/-1}.field-error{display:block;margin-top:.3rem;color:#b42318}.credential-modal__actions{display:flex;justify-content:flex-end;gap:.6rem}.credential-modal__actions button{padding:.75rem 1rem;border:0;border-radius:9px;background:#177245;color:#fff;font-weight:800;cursor:pointer}.credential-modal__actions .secondary{background:#edf2ef;color:#35463c}@media(max-width:900px){.credentials-grid{grid-template-columns:1fr}}@media(max-width:560px){.credential-modal form{grid-template-columns:1fr}.credential-modal label{grid-column:1}.credential-card>header{grid-template-columns:auto 1fr}.credential-card>header button{grid-column:1/-1;justify-content:center}}
</style>
