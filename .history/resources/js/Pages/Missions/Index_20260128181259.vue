<template>
    <AppLayout>
        <div class="missions">

            <h2>Gestion des missions</h2>

            <!-- FILTRES -->
            <div class="filters-header">
                <div class="filters">
                    <!-- Recherche -->
                    <input v-model="filters.search" type="text" placeholder="Rechercher une mission..."
                        class="filter-input" />

                    <!-- Statuts (chips animées) -->
                    <div class="chips">
                        <span v-for="s in statusList" :key="s.value" class="chip"
                            :class="{ active: filters.statuses.includes(s.value) }" @click="toggleStatus(s.value)">
                            {{ s.label }}
                        </span>
                    </div>

                    <!-- Date range -->
                    <div class="date-range">
                        <input type="date" v-model="filters.date_start" class="filter-input" />
                        <input type="date" v-model="filters.date_end" class="filter-input" />
                    </div>

                    <!-- Prestataire -->
                    <select v-model="filters.prestataire_id" class="filter-select">
                        <option value="">Tous les prestataires</option>
                        <option v-for="p in prestataires" :key="p.id" :value="p.id">
                            {{ p.first_name }} {{ p.last_name }}
                        </option>
                    </select>

                    <!-- Prix -->
                    <div class="price-range">
                        <input type="number" v-model="filters.price_min" placeholder="Prix min" class="filter-input" />
                        <input type="number" v-model="filters.price_max" placeholder="Prix max" class="filter-input" />
                    </div>

                    <!-- Catégorie -->
                    <select v-model="filters.category" class="filter-select">
                        <option value="">Catégorie</option>
                        <option v-for="c in categories" :key="c">{{ c }}</option>
                    </select>

                    <!-- Localisation -->
                    <select v-model="filters.location" class="filter-select">
                        <option value="">Localisation</option>
                        <option v-for="l in locations" :key="l">{{ l }}</option>
                    </select>

                    <button @click="loadMissions">Filtrer</button>
                </div>
            </div>

            <!-- LISTE des missions -->
            <div class="missions-container">
                <div v-for="mission in missions" :key="mission.id" class="mission-card">
                    <h3 class="mission-title">{{ truncate(mission.title, 20) }}</h3>
                    <div class="dates" :class="getDurationColor(mission.date_start, mission.date_end)">

                        <div class="date-item">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 3v2.25M17.25 3v2.25M3 9h18M4.5 7.5h15 M6 12h.008v.008H6V12Zm3 0h.008v.008H9V12Zm3 0h.008v.008H12V12Z" />
                            </svg>
                            <span class="label">Début</span>
                            <span class="value">{{ formatDate(mission.date_start) }}</span>
                        </div>

                        <div class="date-item">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v6l4 2M12 3a9 9 0 110 18 9 9 0 010-18z" />
                            </svg>
                            <span class="label">Fin</span>
                            <span class="value">{{ formatDate(mission.date_end) }}</span>
                        </div>

                    </div>

                    <p class="status-badge" :class="mission.status"> Status : {{ mission.status }}</p>

                    <p>{{ mission.description }}</p>
                </div>

                <!-- PAGINATION -->
                <div ref="loadMoreTrigger" class="loader">
                    <span v-if="loading">Chargement...</span>
                    <span v-else-if="noMore">Fin des missions</span>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, watch, onMounted, onBeforeUnmount } from "vue"
import missionService from "@/composables/missionService"

defineProps({
    prestataires: Object
})

const filters = ref({
    search: "",
    statuses: [], // ex: ["pending", "completed"]
    date_start: null,
    date_end: null,
    prestataire_id: null,
    price_min: null,
    price_max: null,
    category: "",
    location: ""
})

const statusList = [
    { label: "En attente", value: "pending" },
    { label: "En cours", value: "in_progress" },
    { label: "Terminée", value: "completed" },
    { label: "Annulée", value: "cancelled" }
]

const categories = ["Électricité", "Plomberie", "Ménage", "Sécurité", "Transport"]
const locations = ["Bamako", "Kayes", "Sikasso", "Ségou", "Mopti"]

function toggleStatus(status) {
    const list = filters.value.statuses
    filters.value.statuses = list.includes(status)
        ? list.filter(s => s !== status)
        : [...list, status]
}

// ----------------------
// Missions + Scroll infini
// ----------------------
const missions = ref([])
const page = ref(1)
const loading = ref(false)
const noMore = ref(false)
const loadMoreTrigger = ref(null)
let observer = null

const loadMissions = async () => {
    if (loading.value || noMore.value) return
    loading.value = true

    const response = await missionService.get({
        page: page.value,
        ...filters.value
    })
    const data = await response.data
    missions.value.push(...data.data)

    if (!data.next_page_url) {
        noMore.value = true
    } else {
        page.value++
    }
    loading.value = false
}

// Reset auto quand un filtre change
watch(filters, () => {
    missions.value = []
    page.value = 1
    noMore.value = false
    loadMissions()
}, { deep: true })


function changePage(page) {
    loadMissions(page)
}

onMounted(() => {
    loadMissions()
    observer = new IntersectionObserver(entries => {
        if (entries[0].isIntersecting) {
            loadMissions()
        }
    })

    if (loadMoreTrigger.value) {
        observer.observe(loadMoreTrigger.value)
    }
})

onBeforeUnmount(() => {
    if (observer && loadMoreTrigger.value) {
        observer.unobserve(loadMoreTrigger.value)
    }
})

// ----------------------
// Utils
// ----------------------
function formatDate(dateStr) {
    const date = new Date(dateStr)
    return date.toLocaleDateString("fr-FR", {
        day: "2-digit",
        month: "short",
        year: "numeric"
    }).replace('.', '') // retire le point après "janv."
}

function getDurationColor(start, end) {
    const d1 = new Date(start)
    const d2 = new Date(end)
    const diffDays = (d2 - d1) / (1000 * 60 * 60 * 24)

    if (diffDays <= 3) return "short"
    if (diffDays <= 10) return "medium"
    return "long"
}

function truncate(text, max = 30) {
    if (!text) return ""
    return text.length > max ? text.substring(0, max) + "…" : text
}

</script>

<style lang="scss" scoped>
.missions {
    padding: 2rem;
    background: #f9fafb;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);

    h2 {
        font-size: 1.6rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: #111827;
    }

    /* FILTRES */
    .filters-header {
        position: sticky;
        top: 4rem;
        z-index: 50;
        background: white;
        padding: 1rem 0;
        border-bottom: 1px solid #e5e7eb;
        backdrop-filter: blur(6px);
    }

    .filters {
        display: flex;
        flex-wrap: wrap;
        gap: 0.8rem;
        padding: 0.5rem 1rem;

        .filter-input,
        .filter-select {
            padding: 0.6rem 0.8rem;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #fff;
            font-size: 0.9rem;
            min-width: 140px;

            &:focus {
                border-color: #2563eb;
                box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
                outline: none;
            }
        }

        .chips {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;

            .chip {
                padding: 0.4rem 0.8rem;
                border-radius: 20px;
                background: #f3f4f6;
                cursor: pointer;
                font-size: 0.8rem;
                font-weight: 600;
                transition: 0.25s ease;

                &:hover {
                    transform: translateY(-2px);
                    background: #e5e7eb;
                }

                &.active {
                    background: #2563eb;
                    color: white;
                    transform: scale(1.05);
                }
            }
        }

        .date-range,
        .price-range {
            display: flex;
            gap: 0.5rem;
        }
    }

    /* LISTE DES MISSIONS */

    // .missions-container {
    //     display: grid;
    //     grid-template-columns: 1fr;
    //     gap: 1.2rem;
    //     padding: 1.5rem;
    //     max-width: 1000px;
    //     margin: auto;

    //     .mission-title {
    //         // white-space: nowrap;
    //         overflow: hidden;
    //         text-overflow: ellipsis;
    //     }

    //     /* Desktop : 2 colonnes */
    //     @media (min-width: 768px) {
    //         grid-template-columns: repeat(2, 1fr);
    //     }

    //     /* Grand écran : 3 colonnes */
    //     @media (min-width: 1200px) {
    //         grid-template-columns: repeat(3, 1fr);
    //     }

    //     .mission-card {
    //         background: #ffffff;
    //         border-radius: 14px;
    //         padding: 1.4rem 1.6rem;
    //         box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
    //         border: 1px solid #f1f1f1;
    //         transition: transform 0.2s ease, box-shadow 0.2s ease;
    //         cursor: pointer;
    //         display: flex;
    //         flex-direction: column;
    //         gap: 0.4rem;

    //         &:hover {
    //             transform: translateY(-3px);
    //             box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    //         }

    //         h3 {
    //             font-size: 1.2rem;
    //             font-weight: 700;
    //             color: #1f2937;
    //             margin-bottom: 0.3rem;
    //         }

    //         p {
    //             margin: 0;
    //             color: #4b5563;
    //             font-size: 0.95rem;
    //             line-height: 1.4;
    //         }

    //         .dates {
    //             display: flex;
    //             flex-direction: column;
    //             gap: 0.6rem;
    //             padding: 0.8rem 1rem;
    //             border-radius: 12px;
    //             background: #f9fafb;
    //             transition: background 0.3s ease;

    //             @media (min-width: 500px) {
    //                 flex-direction: row;
    //                 justify-content: space-between;
    //             }

    //             /* Couleurs dynamiques selon la durée */
    //             &.short {
    //                 border-left: 4px solid #10b981;
    //                 /* vert */
    //             }

    //             &.medium {
    //                 border-left: 4px solid #f59e0b;
    //                 /* orange */
    //             }

    //             &.long {
    //                 border-left: 4px solid #ef4444;
    //                 /* rouge */
    //             }

    //             .date-item {
    //                 display: flex;
    //                 align-items: center;
    //                 gap: 0.4rem;

    //                 .icon {
    //                     width: 20px;
    //                     height: 20px;
    //                     color: #6b7280;
    //                 }

    //                 .label {
    //                     font-size: 0.8rem;
    //                     font-weight: 600;
    //                     color: #6b7280;
    //                 }

    //                 .value {
    //                     font-size: 0.95rem;
    //                     font-weight: 700;
    //                     color: #111827;
    //                 }
    //             }
    //         }


    //         /* Badges de statut */
    //         .status-badge {
    //             display: inline-block;
    //             padding: 0.35rem 0.7rem;
    //             border-radius: 8px;
    //             font-size: 0.8rem;
    //             font-weight: 600;
    //             margin: 0.4rem 0;
    //             color: #fff;
    //             text-transform: capitalize;
    //             width: fit-content;

    //             &.pending {
    //                 background: #f59e0b;
    //             }

    //             &.in_progress {
    //                 background: #3b82f6;
    //             }

    //             &.completed {
    //                 background: #10b981;
    //             }

    //             &.cancelled {
    //                 background: #ef4444;
    //             }
    //         }
    //     }

    //     /* Loader */
    //     .loader {
    //         grid-column: 1 / -1;
    //         /* occupe toute la largeur */
    //         text-align: center;
    //         padding: 1rem;
    //         font-size: 0.95rem;
    //         color: #6b7280;

    //         span {
    //             display: inline-block;
    //             padding: 0.6rem 1rem;
    //             background: #f3f4f6;
    //             border-radius: 8px;
    //         }
    //     }
    // }

    .missions-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
        padding: 1rem;

                h3 {
                font-size: 1.2rem;
                font-weight: 700;
                color: #1f2937;
                margin-bottom: 0.3rem;
            }

            p {
                margin: 0;
                color: #4b5563;
                font-size: 0.95rem;
                line-height: 1.4;
            }

            .dates {
                display: flex;
                flex-direction: column;
                gap: 0.6rem;
                padding: 0.8rem 1rem;
                border-radius: 12px;
                background: #f9fafb;
                transition: background 0.3s ease;

                @media (min-width: 500px) {
                    flex-direction: row;
                    justify-content: space-between;
                }

                /* Couleurs dynamiques selon la durée */
                &.short {
                    border-left: 4px solid #10b981;
                    /* vert */
                }

                &.medium {
                    border-left: 4px solid #f59e0b;
                    /* orange */
                }

                &.long {
                    border-left: 4px solid #ef4444;
                    /* rouge */
                }

                .date-item {
                    display: flex;
                    align-items: center;
                    gap: 0.4rem;

                    .icon {
                        width: 20px;
                        height: 20px;
                        color: #6b7280;
                    }

                    .label {
                        font-size: 0.8rem;
                        font-weight: 600;
                        color: #6b7280;
                    }

                    .value {
                        font-size: 0.95rem;
                        font-weight: 700;
                        color: #111827;
                    }
                }
            }

        @media (min-width: 700px) {
            grid-template-columns: repeat(2, 1fr);
        }

        @media (min-width: 1100px) {
            grid-template-columns: repeat(3, 1fr);
        }

        .mission-card {
            background: white;
            border-radius: 14px;
            padding: 1.2rem;
            border: 1px solid #f1f1f1;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
            transition: 0.25s ease;

            &:hover {
                transform: translateY(-4px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            }
        }

        /* Loader */
        .loader {
            grid-column: 1 / -1;
            /* occupe toute la largeur */
            text-align: center;
            padding: 1rem;
            font-size: 0.95rem;
            color: #6b7280;

            span {
                display: inline-block;
                padding: 0.6rem 1rem;
                background: #f3f4f6;
                border-radius: 8px;
            }
        }
    }

    /* PAGINATION */
    .pagination {
        display: flex;
        align-items: center;
        gap: 1rem;

        button {
            padding: 0.5rem 1rem;
            background: #e5e7eb;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s ease;

            &:hover:not(:disabled) {
                background: #d1d5db;
            }

            &:disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }
        }

        span {
            font-size: 0.95rem;
            color: #374151;
            font-weight: 500;
        }
    }

    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        font-size: 0.8rem;
        font-weight: 600;
        border-radius: 6px;
        text-transform: capitalize;
        color: #fff;
        letter-spacing: 0.3px;

        &.pending {
            background-color: #f59e0b; // jaune
        }

        &.in_progress,
        &.in-progress {
            background-color: #3b82f6; // bleu
        }

        &.completed {
            background-color: #10b981; // vert
        }

        &.cancelled {
            background-color: #ef4444; // rouge
        }
    }

}
</style>
