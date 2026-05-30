<template>

    <section class="mali-map-section">

        <!-- ===================================================== -->
        <!-- HEADER -->
        <!-- ===================================================== -->

        <div class="container">

            <div class="mali-map-section__header">

                <span class="section-badge">
                    🇲🇱 Barasira Mali
                </span>

                <h2 class="section-title">
                    Services disponibles par ville
                </h2>

                <p class="section-subtitle">
                    Découvrez les services disponibles
                    dans plusieurs régions du Mali.
                </p>

            </div>

        </div>

        <!-- ===================================================== -->
        <!-- CONTENT -->
        <!-- ===================================================== -->

        <div class="container">

            <div class="mali-map-wrapper">

                <!-- MAP -->

                <div class="mali-map-card">

                    <div class="mali-map-card__header">

                        <h3>
                            Carte interactive
                        </h3>

                        <span>
                            Cliquez sur une ville
                        </span>

                    </div>

                    <div
                        class="mali-map"
                        v-html="svgContent"
                        @click="handleClick"
                    />

                </div>

                <!-- SIDEBAR -->

                <aside class="mali-map-sidebar">

                    <div
                        v-if="selectedCity"
                        class="city-services-card"
                    >

                        <!-- CITY -->

                        <div class="city-services-card__header">

                            <div class="city-services-card__icon">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>

                            <div>

                                <h3>
                                    {{ selectedCity }}
                                </h3>

                                <p>
                                    {{
                                        servicesByCity[selectedCity]?.length ?? 0
                                    }}
                                    services disponibles
                                </p>

                            </div>

                        </div>

                        <!-- SERVICES -->

                        <ul class="city-services-list">

                            <li
                                v-for="service in servicesByCity[selectedCity]"
                                :key="service"
                            >

                                <i class="bi bi-check-circle-fill"></i>

                                {{ service }}

                            </li>

                        </ul>

                        <!-- ACTION -->

                        <Link
                            href="/services"
                            class="btn btn-primary btn-block"
                        >

                            Voir les services

                            <i class="bi bi-arrow-right"></i>

                        </Link>

                    </div>

                    <!-- EMPTY -->

                    <div
                        v-else
                        class="city-placeholder"
                    >

                        <div class="city-placeholder__icon">
                            🗺️
                        </div>

                        <h3>
                            Sélectionnez une ville
                        </h3>

                        <p>
                            Cliquez sur une région de la carte
                            pour voir les services disponibles.
                        </p>

                    </div>

                </aside>

            </div>

        </div>

    </section>

</template>

<script setup>
import { ref } from 'vue'

import { Link } from '@inertiajs/vue3'

import svgContent from '/public/images/svg/mali-map.svg?raw'

/* =========================================================
   STATE
========================================================= */

const selectedCity = ref(null)

/* =========================================================
   SERVICES
========================================================= */

const servicesByCity = {
    Bamako: [
        'Plomberie',
        'Électricité',
        'Transport',
        'Ménage',
        'Informatique',
    ],

    Kayes: [
        'Transport',
        'Maçonnerie',
        'Livraison',
    ],

    Sikasso: [
        'Couture',
        'Agriculture',
        'Jardinage',
    ],

    Ségou: [
        'Menuiserie',
        'Plomberie',
        'Peinture',
    ],

    Mopti: [
        'Pêche',
        'Transport',
        'Logistique',
    ],

    Tombouctou: [
        'Tourisme',
        'Guides',
        'Transport',
    ],

    Gao: [
        'Logistique',
        'Sécurité',
        'Maintenance',
    ],
}

/* =========================================================
   CLICK MAP
========================================================= */

function handleClick(event) {

    const cityGroup = event.target.closest('.city')

    if (!cityGroup) {
        return
    }

    const city =
        cityGroup.dataset.city ||
        cityGroup.id

    if (!city) {
        return
    }

    selectedCity.value = city
}
</script>
