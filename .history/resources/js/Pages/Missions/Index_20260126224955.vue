<template>
    <AppLayout>
        <div class="missions">

            <h2>Gestion des missions</h2>

            <!-- FILTRES -->
            <div class="filters">
                <select v-model="filters.status">
                    <option value="all">Tous les statuts</option>
                    <option value="pending">En attente</option>
                    <option value="in_progress">En cours</option>
                    <option value="completed">Terminée</option>
                    <option value="cancelled">Annulée</option>
                </select>

                <input type="date" v-model="filters.date" />

                <select v-model="filters.prestataire_id">
                    <option value="">Tous les prestataires</option>
                    <option v-for="p in prestataires" :key="p.id" :value="p.id">
                        {{ p.first_name }} {{ p.last_name }}
                    </option>
                </select>

                <button @click="loadMissions">Filtrer</button>
            </div>

            <!-- LISTE -->
            <!-- <ul id="missions"></ul>
            <div id="loader" style="text-align:center; padding:20px;">Chargement...</div> -->
            <!-- <ul>
                <li v-for="mission in missions.data" :key="mission.id">
                    <strong>{{ mission.title }}</strong>
                    <span class="status-badge" :class="mission.status"> Status :  {{ mission.status }}</span>
                    <span> Date : {{ mission.date_start }}</span>
                </li>
            </ul> -->

            <div class="missions-container">
                <div v-for="mission in missions" :key="mission.id" class="mission-card">
                    <h3>{{ mission.title }}</h3>
                    <div class="timeline">
                        <div class="timeline-item">
                            <span class="label">Début</span>
                            <span class="value">{{ mission.date_start }}</span>
                        </div>

                        <div class="line"></div>

                        <div class="timeline-item">
                            <span class="label">Fin</span>
                            <span class="value">{{ mission.date_end }}</span>
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
import { ref, onMounted, onBeforeUnmount } from "vue"
import missionService from "@/composables/missionService"

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

defineProps({
    prestataires: Object
})

const filters = ref({
    status: "all",
    date: "",
    prestataire_id: ""
})

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
    // missions.value = res.data
}


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
    .filters {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2rem;

        select,
        input[type="date"] {
            padding: 0.6rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #fff;
            font-size: 0.95rem;
            color: #374151;
            transition: border-color 0.2s ease;

            &:focus {
                border-color: #2563eb;
                outline: none;
                box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
            }
        }

        button {
            padding: 0.6rem 1.2rem;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.2s ease;

            &:hover {
                background: #1e4fc7;
            }
        }
    }

    /* LISTE DES MISSIONS */

    .missions-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.2rem;
        padding: 1.5rem;
        max-width: 1000px;
        margin: auto;

        /* Desktop : 2 colonnes */
        @media (min-width: 768px) {
            grid-template-columns: repeat(2, 1fr);
        }

        /* Grand écran : 3 colonnes */
        @media (min-width: 1200px) {
            grid-template-columns: repeat(3, 1fr);
        }

        .mission-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 1.4rem 1.6rem;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
            border: 1px solid #f1f1f1;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            gap: 0.4rem;

            &:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            }

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

            .timeline {
                display: flex;
                align-items: center;
                gap: 1rem;
                margin: 0.8rem 0;

                .timeline-item {
                    display: flex;
                    flex-direction: column;
                    align-items: center;

                    .label {
                        font-size: 0.75rem;
                        font-weight: 600;
                        color: #6b7280;
                    }

                    .value {
                        font-size: 0.9rem;
                        font-weight: 700;
                        color: #111827;
                    }
                }

                .line {
                    flex: 1;
                    height: 2px;
                    background: linear-gradient(90deg, #3b82f6, #8b5cf6);
                    border-radius: 2px;
                }
            }

            /* Badges de statut */
            .status-badge {
                display: inline-block;
                padding: 0.35rem 0.7rem;
                border-radius: 8px;
                font-size: 0.8rem;
                font-weight: 600;
                margin: 0.4rem 0;
                color: #fff;
                text-transform: capitalize;
                width: fit-content;

                &.pending {
                    background: #f59e0b;
                }

                &.in_progress {
                    background: #3b82f6;
                }

                &.completed {
                    background: #10b981;
                }

                &.cancelled {
                    background: #ef4444;
                }
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
