export const emptyMissionForm = () => ({
    service_id: '',
    title: '',
    description: '',
    city: '',
    address: '',
    latitude: null,
    longitude: null,
    price: '',
    date_start: '',
    date_end: '',
    skills: [],
    questions: [],
})

export function normalizeMissionPayload(form) {
    return {
        ...form,
        service_id: form.service_id ? Number(form.service_id) : null,
        price: form.price !== '' && form.price !== null ? Number(form.price) : null,
        latitude: form.latitude !== '' && form.latitude !== null ? Number(form.latitude) : null,
        longitude: form.longitude !== '' && form.longitude !== null ? Number(form.longitude) : null,
        skills: form.skills.filter(Boolean),
        questions: form.questions.filter(Boolean),
    }
}

export function firstValidationError(errors, field) {
    const value = errors[field]
    return Array.isArray(value) ? value[0] : (value ?? '')
}

export function toDateTimeInput(value) {
    if (!value) return ''
    const date = new Date(value)
    const offset = date.getTimezoneOffset() * 60_000
    return new Date(date.getTime() - offset).toISOString().slice(0, 16)
}

export function minimumDateTime() {
    return toDateTimeInput(new Date())
}

export function validateMissionForm(form, t = key => key) {
    const errors = {}

    if (!form.title.trim()) errors.title = [t('missions.validation.titleRequired')]
    if (!form.service_id) errors.service_id = [t('missions.validation.serviceRequired')]
    if (!form.description.trim()) errors.description = [t('missions.validation.descriptionRequired')]
    if (!form.city.trim()) errors.city = [t('missions.validation.cityRequired')]
    if (!form.address.trim()) errors.address = [t('missions.validation.addressRequired')]
    if (!form.date_start) errors.date_start = [t('missions.validation.startDateRequired')]
    if (form.date_end && form.date_start && form.date_end < form.date_start) {
        errors.date_end = [t('missions.validation.endDateAfterStart')]
    }

    return errors
}
