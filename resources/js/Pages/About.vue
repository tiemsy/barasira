<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import DashboardIcon from '@/Components/DashboardIcon.vue'
</script>

<template>
    <Head :title="$t('about.metaTitle')" />

    <AppLayout>
        <main class="about-page">
            <section class="about-hero">
                <div class="about-container about-hero__content">
                    <div>
                        <span class="about-eyebrow">{{ $t('about.eyebrow') }}</span>
                        <h1>{{ $t('about.title') }}</h1>
                        <p>{{ $t('about.introduction') }}</p>
                        <div class="about-hero__actions">
                            <Link href="/services" class="about-button about-button--primary">
                                {{ $t('about.discoverServices') }}
                            </Link>
                            <Link href="/contact-us" class="about-button about-button--secondary">
                                {{ $t('about.contactUs') }}
                            </Link>
                        </div>
                    </div>

                    <div class="about-hero__mark" aria-hidden="true">
                        <DashboardIcon name="users" />
                    </div>
                </div>
            </section>

            <section class="about-container about-story">
                <div class="about-story__heading">
                    <span class="about-eyebrow">{{ $t('about.storyEyebrow') }}</span>
                    <h2>{{ $t('about.storyTitle') }}</h2>
                </div>
                <div class="about-story__copy">
                    <p>{{ $t('about.storyFirst') }}</p>
                    <p>{{ $t('about.storySecond') }}</p>
                </div>
            </section>

            <section class="about-values">
                <div class="about-container">
                    <div class="about-section-heading">
                        <span class="about-eyebrow">{{ $t('about.valuesEyebrow') }}</span>
                        <h2>{{ $t('about.valuesTitle') }}</h2>
                    </div>

                    <div class="about-values__grid">
                        <article v-for="value in ['trust', 'proximity', 'impact']" :key="value">
                            <span class="about-values__icon">
                                <DashboardIcon :name="value === 'trust' ? 'shield' : value === 'proximity' ? 'users' : 'check'" />
                            </span>
                            <h3>{{ $t(`about.values.${value}.title`) }}</h3>
                            <p>{{ $t(`about.values.${value}.description`) }}</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="about-container about-organization">
                <div class="about-section-heading">
                    <span class="about-eyebrow">{{ $t('about.organization.eyebrow') }}</span>
                    <h2>{{ $t('about.organization.title') }}</h2>
                    <p>{{ $t('about.organization.description') }}</p>
                </div>

                <div class="organization-chart" :aria-label="$t('about.organization.title')">
                    <article class="organization-card organization-card--lead">
                        <span class="organization-card__icon"><DashboardIcon name="users" /></span>
                        <div>
                            <strong>{{ $t('about.organization.leadership.title') }}</strong>
                            <small>{{ $t('about.organization.leadership.description') }}</small>
                        </div>
                    </article>

                    <div class="organization-chart__branches">
                        <article v-for="team in ['technology', 'operations', 'growth', 'support']" :key="team" class="organization-card">
                            <span class="organization-card__icon">
                                <DashboardIcon :name="team === 'technology' ? 'code' : team === 'operations' ? 'services' : team === 'growth' ? 'rating' : 'headset'" />
                            </span>
                            <div>
                                <strong>{{ $t(`about.organization.teams.${team}.title`) }}</strong>
                                <small>{{ $t(`about.organization.teams.${team}.description`) }}</small>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section class="about-container about-cta">
                <div>
                    <span class="about-eyebrow">{{ $t('about.ctaEyebrow') }}</span>
                    <h2>{{ $t('about.ctaTitle') }}</h2>
                    <p>{{ $t('about.ctaDescription') }}</p>
                </div>
                <Link href="/register" class="about-button about-button--primary">
                    {{ $t('about.joinUs') }}
                </Link>
            </section>
        </main>
    </AppLayout>
</template>

<style lang="scss" src="../../scss/pages/_about.scss"></style>
