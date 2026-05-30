<div class="services-page">

    <!-- ========================================================= -->
    <!-- HERO -->
    <!-- ========================================================= -->

    <section class="services-hero">

        <div class="container">

            <h1>Trouver un service</h1>

            <p>
                Recherchez parmi nos prestataires qualifiés et trouvez le service idéal.
            </p>

            <!-- HERO SEARCH -->
            <div class="hero-search">
                <input
                    v-model="keyword"
                    placeholder="Plombier, électricien..."
                    @input="fetchServices(true)"
                />

                <button @click="fetchServices(true)">
                    Rechercher
                </button>
            </div>

        </div>

    </section>

    <!-- ========================================================= -->
    <!-- FILTERS (sticky) -->
    <!-- ========================================================= -->

    <section class="services-filters container">

        <select v-model="city" @change="fetchServices(true)">
            <option :value="null">Toutes les villes</option>
            <option
                v-for="city in $page.props.cities"
                :key="city.id"
                :value="city.id"
            >
                {{ city.name }}
            </option>
        </select>

        <select v-model="category" @change="fetchServices(true)">
            <option :value="null">Toutes les catégories</option>
            <option
                v-for="cat in $page.props.categories"
                :key="cat.id"
                :value="cat.id"
            >
                {{ cat.name }}
            </option>
        </select>

    </section>

    <!-- ========================================================= -->
    <!-- GRID -->
    <!-- ========================================================= -->

    <section class="services-grid">

        <!-- LOADER -->
        <div v-if="loading" class="loader">
            Chargement des services...
        </div>

        <!-- EMPTY -->
        <div v-else-if="services.length === 0" class="services-empty">
            <i class="bi bi-search services-empty-icon"></i>
            <h3>Aucun service trouvé</h3>
            <p>Essayez de modifier vos filtres ou votre recherche.</p>
        </div>

        <!-- CARDS -->
        <ServiceCard
            v-else
            v-for="service in services"
            :key="service.id"
            :service="service"
        />

    </section>

</div>
