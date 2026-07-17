<template>
    <div class="translate-widget">
        <button type="button" class="btn btn-outline" :disabled="loading || !text" @click="translate">
            {{ loading ? $t('missions.translation.loading') : label }}
        </button>
        <p v-if="error" class="error">{{ error }}</p>
        <div v-if="translatedText" class="translation">
            <p class="translate-text">{{ translatedText }}</p>
            <button type="button" class="btn btn-outline" @click="$emit('apply', translatedText)">{{ $t('missions.translation.apply') }}</button>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { api } from '@/lib/api'
import { useI18n } from 'vue-i18n'

const props = defineProps({
    text: { type: String, required: true },
    sourceLocale: { type: String, default: 'fr' },
    targetLocale: { type: String, required: true },
    label: { type: String, default: '' },
    context: { type: Object, default: () => ({}) },
})

defineEmits(['apply'])
const { t } = useI18n()
const loading = ref(false)
const error = ref('')
const translatedText = ref('')

const translate = async () => {
    loading.value = true
    error.value = ''
    try {
        const { data } = await api.post('/ai/translate', {
            text: props.text,
            source_locale: props.sourceLocale,
            target_locale: props.targetLocale,
            context: props.context,
        })
        translatedText.value = data.translation
    } catch (e) {
        error.value = t('missions.translation.error')
    } finally {
        loading.value = false
    }
}
</script>
