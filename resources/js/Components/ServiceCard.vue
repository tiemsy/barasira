<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useServiceCategoryLabel } from '@/composables/useServiceCategoryLabel'

const props = defineProps({
    service: { type: Object, required: true },
})
const { locale, t } = useI18n()
const { categoryLabel } = useServiceCategoryLabel()

const price = computed(() => {
    const format = value => new Intl.NumberFormat(locale.value).format(Number(value))
    if (!props.service.price_min && !props.service.price_max) return t('serviceCard.priceOnRequest')
    if (props.service.price_min === props.service.price_max) {
        return `${format(props.service.price_min)} FCFA`
    }
    return `${format(props.service.price_min)} – ${format(props.service.price_max)} FCFA`
})
const providerName = computed(() =>
    `${props.service.user?.first_name ?? ''} ${props.service.user?.last_name ?? ''}`.trim()
)
const providerInitials = computed(() =>
    `${props.service.user?.first_name?.charAt(0) ?? ''}${props.service.user?.last_name?.charAt(0) ?? ''}`.toUpperCase()
)
</script>

<template>
    <article class="service-card">
        <div class="service-card__top">
            <span class="service-card__icon">
                <i :class="service.icon || service.category?.icon || 'bi bi-grid'" />
            </span>
            <span class="service-card__availability">{{ $t('serviceCard.available') }}</span>
        </div>

        <span class="service-card__category">{{ service.category?.name ? categoryLabel(service.category.name) : $t('serviceCard.service') }}</span>
        <h2>{{ service.name }}</h2>
        <p class="service-card__description">{{ service.description }}</p>

        <dl class="service-card__details">
            <div>
                <dt>{{ $t('serviceCard.location') }}</dt>
                <dd>⌖ {{ service.city?.name ?? $t('serviceCard.mali') }}</dd>
            </div>
            <div>
                <dt>{{ $t('serviceCard.estimatedPrice') }}</dt>
                <dd>{{ price }}</dd>
            </div>
        </dl>

        <footer class="service-card__footer">
            <div class="service-card__provider">
                <img v-if="service.user?.avatar_url" :src="service.user.avatar_url" alt="">
                <span v-else>{{ providerInitials || '?' }}</span>
                <div>
                    <strong>{{ providerName || $t('serviceCard.provider') }}</strong>
                    <small>★ {{ service.user?.rating || $t('serviceCard.newProvider') }}</small>
                </div>
            </div>
            <Link :href="`/services/${service.id}`" class="service-card__link" :aria-label="$t('serviceCard.view', { name: service.name })">
                →
            </Link>
        </footer>
    </article>
</template>
