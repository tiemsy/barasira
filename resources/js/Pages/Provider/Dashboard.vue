<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'

defineProps({
    services: Array,
})


const page = usePage()
const user = page.props[1].auth?.user
</script>

<template>

    <Head title="Dashboard" />

    <AppLayout>
        <!-- HERO -->
        <section class="dashboard-hero">
            <div class="container">
                <h1 class="dashboard-title">
                    👋 Bienvenue dans l'espace prestataire {{ user?.first_name }}
                </h1>
                <p class="dashboard-subtitle">
                    Voici un aperçu vos dernières missions sur Barasira
                </p>
            </div>
        </section>

        <!-- CONTENT -->
        <section class="dashboard-content">
            <div class="container grid">

                <!-- CARD -->
                <div class="dash-card">

                    <ul>
                        <li v-for="service in services" :key="service.id">
                            <h3>📦 Service</h3>
                            <!--<pre>{{ JSON.stringify(service, null, 2) }}</pre>-->
                            {{ service.name }} - {{ service['city'].name }}
                            <ul>
                                <h3>📦 Missions</h3>
                                <li v-for="mission in service['missions']" :key="mission.id">
                                    {{ mission.title }}
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>

                <div class="dash-card">
                    <h3>📦 Mes services</h3>
                    <p>Gérez et publiez vos services</p>
                    <a href="/services" class="btn-primary">Voir</a>
                </div>

                <div class="dash-card">
                    <h3>📨 Messages</h3>
                    <p>Consultez vos discussions</p>
                    <a href="/messages" class="btn-primary">Ouvrir</a>
                </div>

                <div class="dash-card">
                    <h3>⭐ Avis</h3>
                    <p>Notes et retours clients</p>
                    <a href="/reviews" class="btn-primary">Consulter</a>
                </div>

            </div>
        </section>
    </AppLayout>
</template>

<style scoped>
.dashboard-hero {
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    color: #fff;
    padding: 4rem 1rem;
}

.dashboard-title {
    font-size: 2.2rem;
    font-weight: 700;
}

.dashboard-subtitle {
    margin-top: 0.5rem;
    opacity: 0.9;
}

.dashboard-content {
    padding: 4rem 1rem;
}

.grid {
    display: grid;
    gap: 2rem;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
}

.dash-card {
    background: #fff;
    border-radius: 18px;
    padding: 2rem;
    box-shadow: 0 20px 40px rgba(0, 0, 0, .08);
    transition: transform .3s ease, box-shadow .3s ease;
}

.dash-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 30px 60px rgba(0, 0, 0, .12);
}

.btn-primary {
    display: inline-block;
    margin-top: 1rem;
    background: #2563eb;
    color: #fff;
    padding: .6rem 1.4rem;
    border-radius: 999px;
    text-decoration: none;
    font-weight: 600;
}
</style>
