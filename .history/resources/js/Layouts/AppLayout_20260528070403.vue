<template>

    <div class="main-layout">

        <!-- ===================================================== -->
        <!-- NAVBAR -->
        <!-- ===================================================== -->

        <header class="main-layout__header">

            <Navbar />

        </header>

        <!-- ===================================================== -->
        <!-- MAIN -->
        <!-- ===================================================== -->

        <main class="main-layout__main">

            <!-- PAGE HEADER -->

            <section
                v-if="title || subtitle"
                class="page-header"
            >

                <div class="container">

                    <div class="page-header__content">

                        <div>

                            <h1
                                v-if="title"
                                class="page-header__title"
                            >
                                {{ title }}
                            </h1>

                            <p
                                v-if="subtitle"
                                class="page-header__subtitle"
                            >
                                {{ subtitle }}
                            </p>

                        </div>

                        <!-- ACTION SLOT -->

                        <div
                            v-if="$slots.actions"
                            class="page-header__actions"
                        >
                            <slot name="actions" />
                        </div>

                    </div>

                </div>

            </section>

            <!-- PAGE CONTENT -->

            <div class="main-layout__content">

                <slot />

            </div>

        </main>

        <!-- ===================================================== -->
        <!-- FOOTER -->
        <!-- ===================================================== -->

        <footer class="main-layout__footer">

            <Footer />

        </footer>

        <!-- ===================================================== -->
        <!-- FLOATING BACKGROUND -->
        <!-- ===================================================== -->

        <div class="layout-blur layout-blur--top"></div>
        <div class="layout-blur layout-blur--bottom"></div>

    </div>

</template>

<script setup>
import Navbar from '@/Components/Navbar.vue'
import Footer from '@/Components/Footer.vue'

defineProps({
    title: {
        type: String,
        default: '',
    },

    subtitle: {
        type: String,
        default: '',
    },
})
</script>
