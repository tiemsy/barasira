<script setup>
import { nextTick, onBeforeUnmount, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useConfirmDialog } from '@/composables/useConfirmDialog'

const { t } = useI18n()
const { state, accept, cancel } = useConfirmDialog()
const confirmButton = ref(null)

function onKeydown(event) {
    if (event.key === 'Escape' && state.open) cancel()
}

watch(() => state.open, async open => {
    document.body.classList.toggle('confirm-dialog-open', open)
    if (open) {
        await nextTick()
        confirmButton.value?.focus()
    }
})

document.addEventListener('keydown', onKeydown)
onBeforeUnmount(() => {
    document.removeEventListener('keydown', onKeydown)
    document.body.classList.remove('confirm-dialog-open')
})
</script>

<template>
    <Teleport to="body">
        <Transition name="confirm-dialog">
            <div v-if="state.open" class="confirm-dialog__backdrop" @mousedown.self="cancel">
                <section class="confirm-dialog" role="alertdialog" aria-modal="true" aria-labelledby="confirm-dialog-title" aria-describedby="confirm-dialog-message">
                    <button type="button" class="confirm-dialog__close" :aria-label="t('confirmDialog.close')" @click="cancel">×</button>
                    <span class="confirm-dialog__icon" :class="`is-${state.tone}`" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4m0 4h.01"/><path d="M10.3 3.8 2.5 17.2A2 2 0 0 0 4.2 20h15.6a2 2 0 0 0 1.7-2.8L13.7 3.8a2 2 0 0 0-3.4 0Z"/></svg>
                    </span>
                    <h2 id="confirm-dialog-title">{{ state.title || t('confirmDialog.title') }}</h2>
                    <p id="confirm-dialog-message">{{ state.message }}</p>
                    <div class="confirm-dialog__actions">
                        <button type="button" class="is-cancel" @click="cancel">{{ t('confirmDialog.cancel') }}</button>
                        <button ref="confirmButton" type="button" class="is-confirm" :class="`is-${state.tone}`" @click="accept">{{ state.confirmLabel || t('confirmDialog.confirm') }}</button>
                    </div>
                </section>
            </div>
        </Transition>
    </Teleport>
</template>

<style lang="scss" src="../../scss/components/_confirm-dialog.scss"></style>
