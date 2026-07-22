<script setup>
import { computed, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    service: { type: Object, default: null },
    providers: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    cities: { type: Array, default: () => [] },
})
const editing = computed(() => Boolean(props.service?.id))
const form = useForm({
    user_id: props.service?.user_id ?? '',
    service_category_id: props.service?.service_category_id ?? '',
    city_id: props.service?.city_id ?? '',
    municipality_id: props.service?.municipality_id ?? '',
    name: props.service?.name ?? '',
    description: props.service?.description ?? '',
    icon: props.service?.icon ?? '',
    price_min: props.service?.price_min ?? 0,
    price_max: props.service?.price_max ?? 0,
    is_active: props.service?.is_active ?? true,
})
const municipalities = computed(() => props.cities.find(city => Number(city.id) === Number(form.city_id))?.municipalities ?? [])
watch(() => form.city_id, () => {
    if (!municipalities.value.some(item => Number(item.id) === Number(form.municipality_id))) form.municipality_id = ''
})

function submit() {
    if (editing.value) form.put(`/admin/services/${props.service.id}`)
    else form.post('/admin/services')
}
</script>

<template>
    <Head :title="editing ? $t('adminServices.editTitle') : $t('adminServices.createTitle')" />
    <AppLayout>
        <main class="admin-user-form-page admin-service-form-page">
            <header class="admin-user-form-header"><div><span>{{ $t('adminServices.eyebrow') }}</span><h1>{{ editing ? $t('adminServices.editTitle') : $t('adminServices.createTitle') }}</h1><p>{{ editing ? $t('adminServices.editSubtitle') : $t('adminServices.createSubtitle') }}</p></div><Link href="/admin/services">{{ $t('adminServices.back') }}</Link></header>

            <form class="admin-user-form-card" @submit.prevent="submit">
                <section>
                    <header><h2>{{ $t('adminServices.details') }}</h2><p>{{ $t('adminServices.detailsHint') }}</p></header>
                    <div class="admin-user-form-grid">
                        <label><span>{{ $t('adminServices.name') }}</span><input v-model.trim="form.name" required maxlength="150"><small v-if="form.errors.name">{{ form.errors.name }}</small></label>
                        <label><span>{{ $t('adminServices.provider') }}</span><select v-model="form.user_id" required><option value="" disabled>{{ $t('adminServices.selectProvider') }}</option><option v-for="provider in providers" :key="provider.id" :value="provider.id">{{ provider.first_name }} {{ provider.last_name }}</option></select><small v-if="form.errors.user_id">{{ form.errors.user_id }}</small></label>
                        <label><span>{{ $t('adminServices.category') }}</span><select v-model="form.service_category_id" required><option value="" disabled>{{ $t('adminServices.selectCategory') }}</option><option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option></select><small v-if="form.errors.service_category_id">{{ form.errors.service_category_id }}</small></label>
                        <label><span>{{ $t('adminServices.icon') }}</span><input v-model.trim="form.icon" maxlength="100" placeholder="tools"><small v-if="form.errors.icon">{{ form.errors.icon }}</small></label>
                        <label><span>{{ $t('adminServices.city') }}</span><select v-model="form.city_id" required><option value="" disabled>{{ $t('adminServices.selectCity') }}</option><option v-for="city in cities" :key="city.id" :value="city.id">{{ city.name }}</option></select><small v-if="form.errors.city_id">{{ form.errors.city_id }}</small></label>
                        <label><span>{{ $t('adminServices.municipality') }}</span><select v-model="form.municipality_id" :disabled="!form.city_id"><option value="">{{ $t('adminServices.noMunicipality') }}</option><option v-for="item in municipalities" :key="item.id" :value="item.id">{{ item.name }}</option></select><small v-if="form.errors.municipality_id">{{ form.errors.municipality_id }}</small></label>
                        <label class="full"><span>{{ $t('adminServices.description') }}</span><textarea v-model.trim="form.description" rows="6" required maxlength="5000"></textarea><small v-if="form.errors.description">{{ form.errors.description }}</small></label>
                    </div>
                </section>
                <section>
                    <header><h2>{{ $t('adminServices.pricing') }}</h2><p>{{ $t('adminServices.pricingHint') }}</p></header>
                    <div class="admin-user-form-grid">
                        <label><span>{{ $t('adminServices.minimumPrice') }}</span><input v-model.number="form.price_min" type="number" min="0" max="99999999" required><small v-if="form.errors.price_min">{{ form.errors.price_min }}</small></label>
                        <label><span>{{ $t('adminServices.maximumPrice') }}</span><input v-model.number="form.price_max" type="number" min="0" max="99999999" required><small v-if="form.errors.price_max">{{ form.errors.price_max }}</small></label>
                        <label class="admin-user-check"><input v-model="form.is_active" type="checkbox"><span>{{ $t('adminServices.makeActive') }}</span></label>
                    </div>
                </section>
                <footer><Link href="/admin/services">{{ $t('profile.cancel') }}</Link><button type="submit" :disabled="form.processing">{{ form.processing ? $t('profile.saving') : $t('profile.save') }}</button></footer>
            </form>
        </main>
    </AppLayout>
</template>

<style lang="scss" src="../../../../scss/pages/admin/_services.scss"></style>
