<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'

const STORAGE_KEY = 'barasira_accessibility_zoom'
const LEVELS = [1, 1.15, 1.3, 1.5]
const levelIndex = ref(0)
const controlsOpen = ref(false)

const zoomPercentage = computed(() => Math.round(LEVELS[levelIndex.value] * 100))
const canDecrease = computed(() => levelIndex.value > 0)
const canIncrease = computed(() => levelIndex.value < LEVELS.length - 1)

function applyZoom() {
    const zoom = LEVELS[levelIndex.value]
    document.documentElement.style.setProperty('--accessibility-zoom', String(zoom))
    document.body.style.zoom = String(zoom)
    localStorage.setItem(STORAGE_KEY, String(zoom))
}

function increase() {
    if (!canIncrease.value) return
    levelIndex.value += 1
    applyZoom()
}

function decrease() {
    if (!canDecrease.value) return
    levelIndex.value -= 1
    applyZoom()
}

function reset() {
    levelIndex.value = 0
    applyZoom()
}

function closeOnEscape(event) {
    if (event.key === 'Escape') controlsOpen.value = false
}

onMounted(() => {
    const savedZoom = Number.parseFloat(localStorage.getItem(STORAGE_KEY))
    const savedIndex = LEVELS.indexOf(savedZoom)
    levelIndex.value = savedIndex >= 0 ? savedIndex : 0
    applyZoom()
    document.addEventListener('keydown', closeOnEscape)
})

onBeforeUnmount(() => document.removeEventListener('keydown', closeOnEscape))
</script>

<template>
    <aside class="accessibility-zoom" :class="{ 'accessibility-zoom--open': controlsOpen }">
        <div v-if="controlsOpen" id="accessibility-zoom-controls" class="accessibility-zoom__panel">
            <div class="accessibility-zoom__heading">
                <strong>{{ $t('accessibility.zoom.title') }}</strong>
                <button type="button" :aria-label="$t('accessibility.zoom.close')" @click="controlsOpen = false">×</button>
            </div>

            <p>{{ $t('accessibility.zoom.description') }}</p>

            <div class="accessibility-zoom__actions">
                <button
                    type="button"
                    :disabled="!canDecrease"
                    :aria-label="$t('accessibility.zoom.decrease')"
                    @click="decrease"
                >A−</button>
                <output aria-live="polite" aria-atomic="true">{{ zoomPercentage }} %</output>
                <button
                    type="button"
                    :disabled="!canIncrease"
                    :aria-label="$t('accessibility.zoom.increase')"
                    @click="increase"
                >A+</button>
            </div>

            <button type="button" class="accessibility-zoom__reset" :disabled="levelIndex === 0" @click="reset">
                {{ $t('accessibility.zoom.reset') }}
            </button>
        </div>

        <button
            type="button"
            class="accessibility-zoom__trigger"
            :aria-label="$t('accessibility.zoom.open')"
            :aria-expanded="controlsOpen"
            aria-controls="accessibility-zoom-controls"
            @click="controlsOpen = !controlsOpen"
        >
            <span aria-hidden="true">A+</span>
            <small>{{ zoomPercentage }}%</small>
        </button>
    </aside>
</template>

<style scoped>
.accessibility-zoom{position:fixed;z-index:11000;right:1rem;bottom:1rem;display:flex;align-items:flex-end;gap:.65rem;font-family:inherit}.accessibility-zoom__trigger{display:grid;width:58px;height:58px;place-items:center;padding:.35rem;border:2px solid #fff;border-radius:50%;background:#12382c;box-shadow:0 10px 30px rgba(0,0,0,.25);color:#fff;cursor:pointer}.accessibility-zoom__trigger span{font-size:1rem;font-weight:900;line-height:1}.accessibility-zoom__trigger small{font-size:.62rem;line-height:1}.accessibility-zoom__trigger:focus-visible,.accessibility-zoom button:focus-visible{outline:3px solid #f1b84b;outline-offset:3px}.accessibility-zoom__panel{width:min(310px,calc(100vw - 6rem));padding:1rem;border:1px solid #d9e4dd;border-radius:14px;background:#fff;box-shadow:0 16px 45px rgba(12,61,39,.22);color:#17251e}.accessibility-zoom__heading{display:flex;align-items:center;justify-content:space-between;gap:1rem}.accessibility-zoom__heading strong{font-size:1rem}.accessibility-zoom__heading button{padding:.1rem .45rem;border:0;background:transparent;color:#44544b;font-size:1.45rem;cursor:pointer}.accessibility-zoom__panel p{margin:.4rem 0 .9rem;color:#5e6d64;font-size:.82rem;line-height:1.45}.accessibility-zoom__actions{display:grid;grid-template-columns:48px 1fr 48px;align-items:center;gap:.5rem}.accessibility-zoom__actions button{height:44px;border:0;border-radius:9px;background:#176b45;color:#fff;font:inherit;font-weight:900;cursor:pointer}.accessibility-zoom__actions output{text-align:center;font-weight:900}.accessibility-zoom button:disabled{cursor:not-allowed;opacity:.45}.accessibility-zoom__reset{width:100%;margin-top:.7rem;padding:.65rem;border:1px solid #176b45;border-radius:9px;background:#fff;color:#176b45;font:inherit;font-weight:800;cursor:pointer}@media(max-width:640px){.accessibility-zoom{right:.75rem;bottom:.75rem;flex-direction:column}.accessibility-zoom__panel{width:min(310px,calc(100vw - 1.5rem))}}
</style>
