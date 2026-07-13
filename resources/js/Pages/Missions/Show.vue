<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onMounted } from "vue"
// import { useRoute } from "vue-router"
import { router, Link } from '@inertiajs/vue3'
import missionService from "@/composables/missionService"

// const route = useRoute()
// const mission = ref(null)
const loading = ref(true)
const props = defineProps({
    mission: {
        type: Object,
        required: true
    }
})


// Mapping des statuts
const statusStyles = {
    pending: { label: "En attente", class: "badge orange" },
    in_progress: { label: "En cours", class: "badge blue" },
    completed: { label: "Terminée", class: "badge green" },
    cancelled: { label: "Annulée", class: "badge red" }
}

function formatDate(dateStr) {
    const date = new Date(dateStr)
    return date.toLocaleDateString("fr-FR", {
        day: "2-digit",
        month: "long",
        year: "numeric"
    })
}

async function loadMission() {
    loading.value = true
    // const response = await missionService.show(route.params.id)
    // console.log(response);

    // mission.value = mission
    loading.value = false
}

onMounted(() => {
    loadMission()
})
</script>

<template>
    <AppLayout>
        <div class="mission-show">

            <!-- Skeleton Loader -->
            <div v-if="loading" class="skeleton-wrapper">
                <div class="skeleton-title"></div>
                <div class="skeleton-block"></div>
                <div class="skeleton-block"></div>
                <div class="skeleton-block"></div>
            </div>

            <!-- Mission -->
            <div v-else class="mission-card">

                <!-- Titre + Statut -->
                <div class="header">
                    <h1>{{ mission.title }}</h1>
                    <span :class="statusStyles[mission.status].class">
                        {{ statusStyles[mission.status].label }}
                    </span>
                </div>

                <!-- Infos principales -->
                <div class="info-grid">

                    <div class="info-item">
                        <label>Date de début</label>
                        <p>{{ formatDate(mission.date_start) }}</p>
                    </div>

                    <div class="info-item">
                        <label>Date de fin</label>
                        <p>{{ formatDate(mission.date_end) }}</p>
                    </div>

                    <div class="info-item">
                        <label>Prix</label>
                        <p>{{ mission.price }} FCFA</p>
                    </div>

                    <div class="info-item">
                        <label>Prestataire</label>
                        <p>{{ mission.prestataire?.name ?? "Non assigné" }}</p>
                    </div>

                    <div class="info-item">
                        <label>Catégorie</label>
                        <p>{{ mission.service?.category?.name  }}</p>
                    </div>

                    <div class="info-item">
                        <label>Localisation</label>
                        <p>{{ mission.address }}</p>
                    </div>

                </div>

                <!-- Description -->
                <div class="description">
                    <h2>Description</h2>
                    <p>{{ mission.description }}</p>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<style scoped lang="scss">
.mission-show {
    padding: 1.5rem;
    max-width: 900px;
    margin: auto;
}

/* ============================
   Skeleton Loader
============================ */
.skeleton-wrapper {
    display: flex;
    flex-direction: column;
    gap: 1rem;

    .skeleton-title {
        height: 28px;
        width: 60%;
        border-radius: 6px;
        background: linear-gradient(90deg, #f3f4f6 0%, #e5e7eb 50%, #f3f4f6 100%);
        animation: shimmer 1.5s infinite;
    }

    .skeleton-block {
        height: 18px;
        width: 100%;
        border-radius: 6px;
        background: linear-gradient(90deg, #f3f4f6 0%, #e5e7eb 50%, #f3f4f6 100%);
        animation: shimmer 1.5s infinite;
    }
}

@keyframes shimmer {
    0% {
        background-position: 200% 0;
    }

    100% {
        background-position: -200% 0;
    }
}

/* ============================
   Mission Card
============================ */
.mission-card {
    background: white;
    padding: 1.8rem;
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid #f1f1f1;
}

/* Header */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.4rem;

    h1 {
        font-size: 1.6rem;
        font-weight: 700;
        color: #1f2937;
    }
}

/* Badges */
.badge {
    padding: 0.45rem 0.9rem;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    color: white;

    &.orange {
        background: #f59e0b;
    }

    &.blue {
        background: #2563eb;
    }

    &.green {
        background: #10b981;
    }

    &.red {
        background: #ef4444;
    }
}

/* Grid infos */
.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.2rem;
    margin-bottom: 2rem;

    @media (max-width: 600px) {
        grid-template-columns: 1fr;
    }

    .info-item {
        background: #f9fafb;
        padding: 1rem;
        border-radius: 10px;

        label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #6b7280;
        }

        p {
            margin-top: 0.3rem;
            font-size: 1rem;
            font-weight: 600;
            color: #1f2937;
        }
    }
}

/* Description */
.description {
    margin-top: 1.5rem;

    h2 {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 0.6rem;
    }

    p {
        line-height: 1.6;
        color: #374151;
    }
}
</style>
