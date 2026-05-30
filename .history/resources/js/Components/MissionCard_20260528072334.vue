<template>

    <article class="mission-card">

        <!-- ===================================================== -->
        <!-- HEADER -->
        <!-- ===================================================== -->

        <div class="mission-card__header">

            <!-- STATUS -->

            <div class="mission-card__badges">

                <span
                    :class="[
                        'mission-card__status',
                        `mission-card__status--${mission.status || 'open'}`
                    ]"
                >

                    <span class="dot"></span>

                    {{ translatedStatus }}

                </span>

                <span
                    v-if="mission.category?.name"
                    class="mission-card__category"
                >

                    <i class="bi bi-briefcase"></i>

                    {{ mission.category.name }}

                </span>

            </div>

            <!-- TITLE -->

            <h3 class="mission-card__title">

                {{ mission.title }}

            </h3>

            <!-- DESCRIPTION -->

            <p
                v-if="mission.description"
                class="mission-card__description"
            >

                {{ truncate(mission.description, 120) }}

            </p>

        </div>

        <!-- ===================================================== -->
        <!-- META -->
        <!-- ===================================================== -->

        <div class="mission-card__meta">

            <!-- CITY -->

            <div class="mission-card__meta-item">

                <i class="bi bi-geo-alt"></i>

                <span>
                    {{
                        mission.address ??
                        mission.city?.name ??
                        'Adresse non précisée'
                    }}
                </span>

            </div>

            <!-- PRICE -->

            <div class="mission-card__meta-item">

                <i class="bi bi-cash-stack"></i>

                <span class="price">

                    {{ formatPrice(mission.price) }}

                    FCFA

                </span>

            </div>

            <!-- DATE -->

            <div
                v-if="mission.created_at"
                class="mission-card__meta-item"
            >

                <i class="bi bi-calendar-event"></i>

                <span>

                    {{ formattedDate }}

                </span>

            </div>

        </div>

        <!-- ===================================================== -->
        <!-- FOOTER -->
        <!-- ===================================================== -->

        <div class="mission-card__footer">

            <!-- CLIENT -->

            <div class="mission-card__user">

                <div class="mission-card__avatar">

                    <img
                        v-if="mission.user?.avatar_url"
                        :src="mission.user.avatar_url"
                        :alt="mission.user?.first_name"
                    />

                    <div
                        v-else
                        class="mission-card__avatar-placeholder"
                    >
                        <i class="bi bi-person"></i>
                    </div>

                </div>

                <div class="mission-card__user-info">

                    <strong>

                        {{
                            mission.user?.first_name ??
                            'Client'
                        }}

                    </strong>

                    <span>
                        Client Barasira
                    </span>

                </div>

            </div>

            <!-- ACTIONS -->

            <div class="mission-card__actions">

                <Link
                    :href="`/missions/${mission.id}`"
                    class="btn btn-outline"
                >

                    Voir détails

                </Link>

                <Link
                    v-if="canApply"
                    :href="`/missions/${mission.id}`"
                    class="btn btn-primary"
                >

                    <i class="bi bi-send"></i>

                    Postuler

                </Link>

            </div>

        </div>

    </article>

</template>

<script setup>
import { computed } from 'vue'

import {
    Link,
    usePage
} from '@inertiajs/vue3'

/* =========================================================
   PROPS
========================================================= */

const props = defineProps({
    mission: {
        type: Object,
        required: true,
    },
})

/* =========================================================
   USER
========================================================= */

const page = usePage()

const user = computed(() => {
    return page.props.auth?.user ?? null
})

/* =========================================================
   APPLY
========================================================= */

const canApply = computed(() => {

    return (
        user.value &&
        user.value.role === 'prestataire'
    )
})

/* =========================================================
   STATUS
========================================================= */

const translatedStatus = computed(() => {

    const labels = {
        open: 'Ouverte',
        assigned: 'Assignée',
        done: 'Terminée',
    }

    return labels[
        props.mission.status
    ] ?? 'Ouverte'
})

/* =========================================================
   DATE
========================================================= */

const formattedDate = computed(() => {

    if (!props.mission.created_at) {
        return null
    }

    return new Date(
        props.mission.created_at
    ).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    })
})

/* =========================================================
   PRICE
========================================================= */

const formatPrice = (value) => {

    if (!value) {
        return '0'
    }

    return new Intl.NumberFormat(
        'fr-FR'
    ).format(value)
}

/* =========================================================
   TRUNCATE
========================================================= */

const truncate = (
    text,
    length = 120
) => {

    if (!text) {
        return ''
    }

    return text.length > length
        ? `${text.slice(0, length)}...`
        : text
}
</script>
