<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { computed } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'

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
                                <i class="fas fa-headset"></i>
                            </div>

                            <div class="contact-hero__bubble contact-hero__bubble--one">
                                <i class="fas fa-comment-dots"></i>
                            </div>

                            <div class="contact-hero__bubble contact-hero__bubble--two">
                                <i class="fas fa-envelope"></i>
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
                                    <i class="fas fa-envelope"></i>
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
                                    <i class="fas fa-phone-alt"></i>
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
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>

                                <div class="contact-card__content">
                                    <span>{{ $t('contact.information.area') }}</span>
                                    <strong>{{ $t('contact.information.country') }}</strong>
                                </div>
                            </div>

                            <div class="contact-card">
                                <div class="contact-card__icon">
                                    <i class="fas fa-clock"></i>
                                </div>

                                <div class="contact-card__content">
                                    <span>{{ $t('contact.information.hoursLabel') }}</span>
                                    <strong>{{ $t('contact.information.hours') }}</strong>
                                </div>
                            </div>

                            <div v-if="!user" class="contact-help">
                                <div class="contact-help__icon">
                                    <i class="fas fa-lightbulb"></i>
                                </div>

                                <div>
                                    <h3>{{ $t('contact.account.title') }}</h3>

                                    <p>
                                        {{ $t('contact.account.description') }}
                                    </p>

                                    <Link href="/login" class="contact-help__link">
                                        {{ $t('contact.account.login') }}
                                        <i class="fas fa-arrow-right"></i>
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
                                <i class="fas fa-check-circle"></i>

                                <span>
                                    {{ $t('contact.form.success') }}
                                </span>
                            </div>

                            <div v-if="$page.props.flash?.error" class="alert alert-error" role="alert">
                                <i class="fas fa-exclamation-circle"></i>

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
                                            <i class="fas fa-user"></i>

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
                                            <i class="fas fa-envelope"></i>

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
                                            <i class="fas fa-phone"></i>

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
                                            <i class="fas fa-users"></i>

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

                                            <i class="fas fa-chevron-down select-arrow"></i>
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
                                            <i class="fas fa-tag"></i>

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
                                                <i class="fas fa-check"></i>
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
                                        <i class="fas fa-spinner fa-spin"></i>
                                        {{ $t('contact.form.sending') }}
                                    </template>

                                    <template v-else>
                                        {{ $t('contact.form.submit') }}
                                        <i class="fas fa-paper-plane"></i>
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
                            <i class="fas fa-envelope"></i>
                        </a>
                    </div>
                </div>
            </section>
        </main>
    </AppLayout>
</template>
