<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { computed } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import DashboardIcon from '@/Components/DashboardIcon.vue'

const page = usePage()
const user = computed(() => page.props?.auth?.user ?? null)
const contactProfiles = ['client', 'prestataire']

const form = useForm({
    name: user.value?.name ?? '',
    email: user.value?.email ?? '',
    phone: user.value?.phone ?? '',
    user_type: contactProfiles.includes(user.value?.role) ? user.value.role : '',
    subject: '',
    message: '',
    consent: false,
})

defineProps({
    contactEmail: {
        type: String,
        default: 'contact@barasira.com',
    },
    contactPhone: {
        type: String,
        default: '+223 00 00 00 00',
    },
})

const messageLength = computed(() => form.message.length)

const submit = () => {
    form.post('/contact-us', {
        preserveScroll: true,
        onSuccess: response => {
            if (response.props.flash?.success) {
                form.reset('subject', 'message', 'consent')
            }
        },
    })
}
</script>

<template>

    <Head :title="$t('contact.metaTitle')" />

    <AppLayout>
        <main class="contact-page">
            <section class="contact-hero">
                <div class="contact-container">
                    <div class="contact-hero__breadcrumb">
                        <Link href="/">{{ $t('navigation.home') }}</Link>
                        <span>/</span>
                        <span>{{ $t('footer.contactUs') }}</span>
                    </div>

                    <div class="contact-hero__content">
                        <div class="contact-hero__text">
                            <span class="contact-hero__badge">
                                {{ $t('contact.hero.badge') }}
                            </span>

                            <h1>{{ $t('contact.hero.title') }}</h1>

                            <p>
                                {{ $t('contact.hero.description') }}
                            </p>
                        </div>

                        <div class="contact-hero__illustration" aria-hidden="true">
                            <div class="contact-hero__icon">
                                <DashboardIcon name="headset" />
                            </div>

                            <div class="contact-hero__bubble contact-hero__bubble--one">
                                <DashboardIcon name="messages" />
                            </div>

                            <div class="contact-hero__bubble contact-hero__bubble--two">
                                <DashboardIcon name="mail" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="contact-content">
                <div class="contact-container">
                    <div class="contact-grid">
                        <aside class="contact-informations">
                            <div class="contact-informations__header">
                                <span class="section-label">{{ $t('contact.information.label') }}</span>

                                <h2>{{ $t('contact.information.title') }}</h2>

                                <p>
                                    {{ $t('contact.information.description') }}
                                </p>
                            </div>

                            <div class="contact-card">
                                <div class="contact-card__icon">
                                    <DashboardIcon name="mail" />
                                </div>

                                <div class="contact-card__content">
                                    <span>{{ $t('contact.information.email') }}</span>

                                    <a :href="`mailto:${contactEmail}`">
                                        {{ contactEmail }}
                                    </a>
                                </div>
                            </div>

                            <div class="contact-card">
                                <div class="contact-card__icon">
                                    <DashboardIcon name="phone" />
                                </div>

                                <div class="contact-card__content">
                                    <span>{{ $t('contact.information.phone') }}</span>

                                    <a :href="`tel:${contactPhone.replace(/\s/g, '')}`">
                                        {{ contactPhone }}
                                    </a>
                                </div>
                            </div>

                            <div class="contact-card">
                                <div class="contact-card__icon">
                                    <DashboardIcon name="location" />
                                </div>

                                <div class="contact-card__content">
                                    <span>{{ $t('contact.information.area') }}</span>
                                    <strong>{{ $t('contact.information.country') }}</strong>
                                </div>
                            </div>

                            <div class="contact-card">
                                <div class="contact-card__icon">
                                    <DashboardIcon name="clock" />
                                </div>

                                <div class="contact-card__content">
                                    <span>{{ $t('contact.information.hoursLabel') }}</span>
                                    <strong>{{ $t('contact.information.hours') }}</strong>
                                </div>
                            </div>

                            <div v-if="!user" class="contact-help">
                                <div class="contact-help__icon">
                                    <DashboardIcon name="lightbulb" />
                                </div>

                                <div>
                                    <h3>{{ $t('contact.account.title') }}</h3>

                                    <p>
                                        {{ $t('contact.account.description') }}
                                    </p>

                                    <Link href="/login" class="contact-help__link">
                                        {{ $t('contact.account.login') }}
                                        <DashboardIcon name="arrow" />
                                    </Link>
                                </div>
                            </div>
                        </aside>

                        <div class="contact-form-wrapper">
                            <div class="contact-form-header">
                                <span class="section-label">{{ $t('contact.form.label') }}</span>
                                <h2>{{ $t('contact.form.title') }}</h2>

                                <p>
                                    {{ $t('contact.form.description') }}
                                </p>
                            </div>

                            <div v-if="$page.props.flash?.success" class="alert alert-success" role="status" aria-live="polite">
                                <DashboardIcon name="verified" />

                                <span>
                                    {{ $t('contact.form.success') }}
                                </span>
                            </div>

                            <div v-if="$page.props.flash?.error" class="alert alert-error" role="alert">
                                <DashboardIcon name="alert" />

                                <span>
                                    {{ $t('contact.form.error') }}
                                </span>
                            </div>

                            <form class="contact-form" @submit.prevent="submit">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="name">
                                            {{ $t('contact.form.name') }}
                                            <span>*</span>
                                        </label>

                                        <div class="input-wrapper"
                                            :class="{ 'input-wrapper--error': form.errors.name }">
                                            <DashboardIcon name="profile" />

                                            <input id="name" v-model="form.name" type="text" name="name"
                                                :placeholder="$t('contact.form.namePlaceholder')" autocomplete="name" required
                                                :aria-invalid="Boolean(form.errors.name)" />
                                        </div>

                                        <p v-if="form.errors.name" class="form-error">
                                            {{ form.errors.name }}
                                        </p>
                                    </div>

                                    <div class="form-group">
                                        <label for="email">
                                            {{ $t('contact.form.email') }}
                                            <span>*</span>
                                        </label>

                                        <div class="input-wrapper"
                                            :class="{ 'input-wrapper--error': form.errors.email }">
                                            <DashboardIcon name="mail" />

                                            <input id="email" v-model="form.email" type="email" name="email"
                                                :placeholder="$t('contact.form.emailPlaceholder')" autocomplete="email" required
                                                :aria-invalid="Boolean(form.errors.email)" />
                                        </div>

                                        <p v-if="form.errors.email" class="form-error">
                                            {{ form.errors.email }}
                                        </p>
                                    </div>

                                    <div class="form-group">
                                        <label for="phone">{{ $t('contact.form.phone') }}</label>

                                        <div class="input-wrapper"
                                            :class="{ 'input-wrapper--error': form.errors.phone }">
                                            <DashboardIcon name="phone" />

                                            <input id="phone" v-model="form.phone" type="tel" name="phone"
                                                :placeholder="$t('contact.form.phonePlaceholder')" autocomplete="tel" />
                                        </div>

                                        <p v-if="form.errors.phone" class="form-error">
                                            {{ form.errors.phone }}
                                        </p>
                                    </div>

                                    <div class="form-group">
                                        <label for="user_type">
                                            {{ $t('contact.form.profile') }}
                                        </label>

                                        <div class="input-wrapper"
                                            :class="{ 'input-wrapper--error': form.errors.user_type }">
                                            <DashboardIcon name="users" />

                                            <select id="user_type" v-model="form.user_type" name="user_type">
                                                <option value="">
                                                    {{ $t('contact.form.profilePlaceholder') }}
                                                </option>

                                                <option value="client">
                                                    {{ $t('contact.form.profiles.client') }}
                                                </option>

                                                <option value="prestataire">
                                                    {{ $t('contact.form.profiles.provider') }}
                                                </option>

                                                <option value="visitor">
                                                    {{ $t('contact.form.profiles.visitor') }}
                                                </option>

                                                <option value="partner">
                                                    {{ $t('contact.form.profiles.partner') }}
                                                </option>
                                            </select>

                                            <DashboardIcon name="chevron-down" class="select-arrow" />
                                        </div>

                                        <p v-if="form.errors.user_type" class="form-error">
                                            {{ form.errors.user_type }}
                                        </p>
                                    </div>

                                    <div class="form-group form-group--full">
                                        <label for="subject">
                                            {{ $t('contact.form.subject') }}
                                            <span>*</span>
                                        </label>

                                        <div class="input-wrapper"
                                            :class="{ 'input-wrapper--error': form.errors.subject }">
                                            <DashboardIcon name="tag" />

                                            <input id="subject" v-model="form.subject" type="text" name="subject"
                                                :placeholder="$t('contact.form.subjectPlaceholder')" maxlength="150" required
                                                :aria-invalid="Boolean(form.errors.subject)" />
                                        </div>

                                        <p v-if="form.errors.subject" class="form-error">
                                            {{ form.errors.subject }}
                                        </p>
                                    </div>

                                    <div class="form-group form-group--full">
                                        <div class="form-label-line">
                                            <label for="message">
                                                {{ $t('contact.form.message') }}
                                                <span>*</span>
                                            </label>

                                            <small>
                                                {{ messageLength }}/2000
                                            </small>
                                        </div>

                                        <div class="textarea-wrapper"
                                            :class="{ 'textarea-wrapper--error': form.errors.message }">
                                            <textarea id="message" v-model="form.message" name="message"
                                                minlength="10" maxlength="2000" rows="7" required
                                                :aria-invalid="Boolean(form.errors.message)"
                                                :placeholder="$t('contact.form.messagePlaceholder')"></textarea>
                                        </div>

                                        <p v-if="form.errors.message" class="form-error">
                                            {{ form.errors.message }}
                                        </p>
                                    </div>

                                    <div class="form-group form-group--full">
                                        <label class="checkbox-label">
                                            <input v-model="form.consent" type="checkbox" name="consent" required
                                                :aria-invalid="Boolean(form.errors.consent)" />

                                            <span class="checkbox-custom">
                                                <DashboardIcon name="check" />
                                            </span>

                                            <span class="checkbox-text">
                                                {{ $t('contact.form.consent') }}
                                                <span>*</span>
                                            </span>
                                        </label>

                                        <p v-if="form.errors.consent" class="form-error">
                                            {{ form.errors.consent }}
                                        </p>
                                    </div>
                                </div>

                                <button type="submit" class="submit-button" :disabled="form.processing">
                                    <template v-if="form.processing">
                                        <DashboardIcon name="loading" />
                                        {{ $t('contact.form.sending') }}
                                    </template>

                                    <template v-else>
                                        {{ $t('contact.form.submit') }}
                                        <DashboardIcon name="send" />
                                    </template>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

            <section class="contact-faq">
                <div class="contact-container">
                    <div class="contact-faq__content">
                        <div>
                            <span class="section-label">{{ $t('contact.direct.badge') }}</span>
                            <h2>{{ $t('contact.direct.title') }}</h2>
                            <p>
                                {{ $t('contact.direct.description') }}
                            </p>
                        </div>

                        <a :href="`mailto:${contactEmail}`" class="faq-button">
                            {{ contactEmail }}
                            <DashboardIcon name="mail" />
                        </a>
                    </div>
                </div>
            </section>
        </main>
    </AppLayout>
</template>
