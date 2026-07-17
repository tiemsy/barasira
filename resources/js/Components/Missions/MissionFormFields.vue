<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import TranslateButton from '@/Components/Ai/TranslateButton.vue'
import { firstValidationError } from '@/utils/missionForm'
import { loadGoogleMapsPlaces } from '@/lib/googleMaps'

const props = defineProps({
    form: { type: Object, required: true },
    errors: { type: Object, default: () => ({}) },
    services: { type: Array, default: () => [] },
    minimumDate: { type: String, default: '' },
    translationEnabled: { type: Boolean, default: false },
})

const skill = ref('')
const question = ref('')
const targetLocale = ref('en')
const addressInput = ref(null)
const autocompleteReady = ref(false)
const autocompleteUnavailable = ref(false)
let autocomplete = null
let placeListener = null
const errorFor = field => firstValidationError(props.errors, field)

const canAddSkill = computed(() =>
    skill.value.trim() && props.form.skills.length < 10
)
const canAddQuestion = computed(() =>
    question.value.trim() && props.form.questions.length < 5
)

function addUnique(list, value) {
    const normalized = value.trim()
    if (normalized && !list.some(item => item.toLowerCase() === normalized.toLowerCase())) {
        list.push(normalized)
    }
}

function addSkill() {
    if (!canAddSkill.value) return
    addUnique(props.form.skills, skill.value)
    skill.value = ''
}

function addQuestion() {
    if (!canAddQuestion.value) return
    addUnique(props.form.questions, question.value)
    question.value = ''
}

function addressComponent(place, types) {
    return place.address_components?.find(component =>
        types.some(type => component.types.includes(type))
    )?.long_name ?? ''
}

function applySelectedPlace() {
    const place = autocomplete.getPlace()
    const location = place.geometry?.location

    if (!location) return

    props.form.address = place.formatted_address ?? addressInput.value?.value ?? props.form.address
    props.form.city = addressComponent(place, [
        'locality',
        'administrative_area_level_2',
        'administrative_area_level_1',
    ]) || props.form.city
    props.form.latitude = location.lat()
    props.form.longitude = location.lng()
}

function handleManualAddressInput() {
    props.form.latitude = null
    props.form.longitude = null
}

onMounted(async () => {
    try {
        await loadGoogleMapsPlaces()
        autocomplete = new window.google.maps.places.Autocomplete(addressInput.value, {
            componentRestrictions: { country: 'ml' },
            fields: ['address_components', 'formatted_address', 'geometry'],
            types: ['geocode'],
        })
        placeListener = autocomplete.addListener('place_changed', applySelectedPlace)
        autocompleteReady.value = true
    } catch {
        autocompleteUnavailable.value = true
    }
})

onBeforeUnmount(() => {
    if (placeListener && window.google?.maps) {
        window.google.maps.event.removeListener(placeListener)
    }
})
</script>

<template>
    <div class="form-section">
        <div class="form-section__heading">
            <span>1</span>
            <div>
                <h2>{{ $t('missions.form.needTitle') }}</h2>
                <p>{{ $t('missions.form.needDescription') }}</p>
            </div>
        </div>

        <div class="form-group">
            <label for="title">{{ $t('missions.form.title') }} <em>*</em></label>
            <input id="title" v-model.trim="form.title" type="text" maxlength="255"
                :class="{ invalid: errors.title }" :placeholder="$t('missions.form.titlePlaceholder')">
            <div class="field-meta">
                <span v-if="errors.title" class="error">{{ errorFor('title') }}</span>
                <small>{{ form.title.length }}/255</small>
            </div>
        </div>

        <div class="form-group">
            <label for="service">{{ $t('missions.fields.service') }} <em>*</em></label>
            <select id="service" v-model="form.service_id" :class="{ invalid: errors.service_id }">
                <option value="">{{ $t('missions.form.selectService') }}</option>
                <option v-for="service in services" :key="service.id" :value="String(service.id)">
                    {{ service.name }}
                </option>
            </select>
            <span v-if="errors.service_id" class="error">{{ errorFor('service_id') }}</span>
        </div>

        <div class="form-group">
            <label for="description">{{ $t('missions.fields.description') }} <em>*</em></label>
            <textarea id="description" v-model.trim="form.description" rows="7" maxlength="5000"
                :class="{ invalid: errors.description }"
                :placeholder="$t('missions.form.descriptionPlaceholder')" />
            <div class="field-meta">
                <span v-if="errors.description" class="error">{{ errorFor('description') }}</span>
                <small>{{ form.description.length }}/5000</small>
            </div>
        </div>

        <section v-if="translationEnabled" class="translation-panel">
            <div class="translation-panel__header">
                <div>
                    <h3>{{ $t('missions.translation.title') }}</h3>
                    <p>{{ $t('missions.translation.description') }}</p>
                </div>
                <select v-model="targetLocale" :aria-label="$t('missions.translation.targetLanguage')">
                    <option value="fr">{{ $t('lang.french') }}</option>
                    <option value="bm">{{ $t('lang.bambara') }}</option>
                    <option value="en">{{ $t('lang.english') }}</option>
                </select>
            </div>
            <div class="translation-actions">
                <TranslateButton :text="form.title" source-locale="fr" :target-locale="targetLocale"
                    :label="$t('missions.translation.translateTitle')" :context="{ type: 'mission', field: 'title' }"
                    @apply="form.title = $event" />
                <TranslateButton :text="form.description" source-locale="fr" :target-locale="targetLocale"
                    :label="$t('missions.translation.translateDescription')" :context="{ type: 'mission', field: 'description' }"
                    @apply="form.description = $event" />
            </div>
        </section>
    </div>

    <div class="form-section">
        <div class="form-section__heading">
            <span>2</span>
            <div>
                <h2>{{ $t('missions.form.locationBudgetTitle') }}</h2>
                <p>{{ $t('missions.form.locationBudgetDescription') }}</p>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="city">{{ $t('missions.fields.city') }} <em>*</em></label>
                <input id="city" v-model.trim="form.city" type="text" maxlength="50"
                    :class="{ invalid: errors.city }" :placeholder="$t('missions.form.cityPlaceholder')">
                <span v-if="errors.city" class="error">{{ errorFor('city') }}</span>
            </div>
            <div class="form-group">
                <label for="price">{{ $t('missions.form.estimatedBudget') }}</label>
                <input id="price" v-model="form.price" type="number" min="0" step="500"
                    :class="{ invalid: errors.price }" :placeholder="$t('missions.form.budgetPlaceholder')">
                <span v-if="errors.price" class="error">{{ errorFor('price') }}</span>
            </div>
        </div>
        <div class="form-group">
            <label for="address">{{ $t('missions.form.address') }} <em>*</em></label>
            <div class="mission-address-field">
                <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                <input id="address" ref="addressInput" v-model.trim="form.address" type="text" maxlength="255"
                    autocomplete="off" :class="{ invalid: errors.address }"
                    :placeholder="$t('missions.form.addressPlaceholder')" @input="handleManualAddressInput">
                <span v-if="autocompleteReady" class="mission-address-field__status" :title="$t('missions.form.autocompleteReady')">
                    <i class="fas fa-location-arrow" aria-hidden="true"></i>
                </span>
            </div>
            <small v-if="autocompleteReady" class="field-hint">{{ $t('missions.form.addressAutocompleteHint') }}</small>
            <small v-else-if="autocompleteUnavailable" class="field-hint">{{ $t('missions.form.addressManualHint') }}</small>
            <span v-if="errors.address" class="error">{{ errorFor('address') }}</span>
        </div>
    </div>

    <div class="form-section">
        <div class="form-section__heading">
            <span>3</span>
            <div>
                <h2>{{ $t('missions.form.scheduleTitle') }}</h2>
                <p>{{ $t('missions.form.scheduleDescription') }}</p>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="date_start">{{ $t('missions.form.desiredStart') }} <em>*</em></label>
                <input id="date_start" v-model="form.date_start" type="datetime-local"
                    :min="minimumDate" :class="{ invalid: errors.date_start }">
                <span v-if="errors.date_start" class="error">{{ errorFor('date_start') }}</span>
            </div>
            <div class="form-group">
                <label for="date_end">{{ $t('missions.form.desiredEnd') }}</label>
                <input id="date_end" v-model="form.date_end" type="datetime-local"
                    :min="form.date_start || minimumDate" :class="{ invalid: errors.date_end }">
                <span v-if="errors.date_end" class="error">{{ errorFor('date_end') }}</span>
            </div>
        </div>
    </div>

    <div class="form-section">
        <div class="form-section__heading">
            <span>4</span>
            <div>
                <h2>{{ $t('missions.form.skillsQuestionsTitle') }}</h2>
                <p>{{ $t('missions.form.skillsQuestionsDescription') }}</p>
            </div>
        </div>
        <div class="form-group">
            <label for="skill">{{ $t('missions.form.desiredSkills') }} <small>({{ form.skills.length }}/10)</small></label>
            <div class="inline-entry">
                <input id="skill" v-model="skill" type="text" maxlength="100"
                    :placeholder="$t('missions.form.skillPlaceholder')" @keydown.enter.prevent="addSkill">
                <button type="button" :disabled="!canAddSkill" @click="addSkill">{{ $t('missions.form.add') }}</button>
            </div>
            <div v-if="form.skills.length" class="skills-list">
                <span v-for="(item, index) in form.skills" :key="`${item}-${index}`" class="skill-tag">
                    {{ item }}
                    <button type="button" :aria-label="$t('missions.form.removeSkill', { item })" @click="form.skills.splice(index, 1)">×</button>
                </span>
            </div>
        </div>
        <div class="form-group">
            <label for="question">{{ $t('missions.form.providerQuestions') }} <small>({{ form.questions.length }}/5)</small></label>
            <div class="inline-entry">
                <input id="question" v-model="question" type="text" maxlength="500"
                    :placeholder="$t('missions.form.questionPlaceholder')" @keydown.enter.prevent="addQuestion">
                <button type="button" :disabled="!canAddQuestion" @click="addQuestion">{{ $t('missions.form.add') }}</button>
            </div>
            <ul v-if="form.questions.length" class="question-list">
                <li v-for="(item, index) in form.questions" :key="`${item}-${index}`">
                    <span>{{ item }}</span>
                    <button type="button" :aria-label="$t('missions.form.removeQuestion')" @click="form.questions.splice(index, 1)">×</button>
                </li>
            </ul>
        </div>
    </div>
</template>
