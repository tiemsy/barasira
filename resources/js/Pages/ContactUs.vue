<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { computed } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { capitalize, fullName } from '@/utils/string'

const page = usePage()
const user = computed(() => page.props.auth?.user ?? null)

console.log("AUTH USER", user.value);


const form = useForm({
    name: user.value?.name,
    email: user.value?.email ?? '',
    phone: user.value?.phone ?? '',
    user_type: user.value?.role ?? '',
    subject: '',
    message: '',
    consent: false,
})

const props = defineProps({
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
    form.post('contact-us', {
        preserveScroll: true,

        onSuccess: () => {
            form.reset()
        },

        onError: errors => {
            console.error('Erreurs formulaire', errors)
        },
    })
}
</script>

<template>

    <Head title="Nous contacter" />

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
                                Besoin d’aide ?
                            </span>

                            <h1>Contactez l’équipe Barasira</h1>

                            <p>
                                Une question concernant une mission, votre compte,
                                un prestataire ou le fonctionnement de la plateforme ?
                                Notre équipe est à votre écoute.
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
                                <span class="section-label">Nos coordonnées</span>

                                <h2>Nous sommes disponibles pour vous accompagner</h2>

                                <p>
                                    Vous pouvez nous contacter directement ou utiliser
                                    le formulaire. Nous vous répondrons dans les meilleurs
                                    délais.
                                </p>
                            </div>

                            <div class="contact-card">
                                <div class="contact-card__icon">
                                    <i class="fas fa-envelope"></i>
                                </div>

                                <div class="contact-card__content">
                                    <span>E-mail</span>

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
                                    <span>Téléphone</span>

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
                                    <span>Zone d’intervention</span>
                                    <strong>Mali</strong>
                                </div>
                            </div>

                            <div class="contact-card">
                                <div class="contact-card__icon">
                                    <i class="fas fa-clock"></i>
                                </div>

                                <div class="contact-card__content">
                                    <span>Horaires</span>
                                    <strong>Lundi au samedi, de 8h à 18h</strong>
                                </div>
                            </div>

                            <div class="contact-help">
                                <div class="contact-help__icon">
                                    <i class="fas fa-lightbulb"></i>
                                </div>

                                <div>
                                    <h3>Vous avez déjà un compte ?</h3>

                                    <p>
                                        Connectez-vous afin que nous puissions identifier
                                        plus rapidement votre demande.
                                    </p>

                                    <Link href="/login" class="contact-help__link">
                                        Se connecter
                                        <i class="fas fa-arrow-right"></i>
                                    </Link>
                                </div>
                            </div>
                        </aside>

                        <div class="contact-form-wrapper">
                            <div class="contact-form-header">
                                <span class="section-label">Envoyer un message</span>
                                <h2>Comment pouvons-nous vous aider ?</h2>

                                <p>
                                    Complétez le formulaire ci-dessous en décrivant
                                    précisément votre demande.
                                </p>
                            </div>

                            <div v-if="$page.props.flash?.success" class="alert alert-success">
                                <i class="fas fa-check-circle"></i>

                                <span>
                                    {{ $page.props.flash.success }}
                                </span>
                            </div>

                            <div v-if="$page.props.flash?.error" class="alert alert-error">
                                <i class="fas fa-exclamation-circle"></i>

                                <span>
                                    {{ $page.props.flash.error }}
                                </span>
                            </div>

                            <form class="contact-form" @submit.prevent="submit">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="name">
                                            Nom complet
                                            <span>*</span>
                                        </label>

                                        <div class="input-wrapper"
                                            :class="{ 'input-wrapper--error': form.errors.name }">
                                            <i class="fas fa-user"></i>

                                            <input id="name" v-model="form.name" type="text" name="name"
                                                placeholder="Votre nom complet" autocomplete="name" />
                                        </div>

                                        <p v-if="form.errors.name" class="form-error">
                                            {{ form.errors.name }}
                                        </p>
                                    </div>

                                    <div class="form-group">
                                        <label for="email">
                                            Adresse e-mail
                                            <span>*</span>
                                        </label>

                                        <div class="input-wrapper"
                                            :class="{ 'input-wrapper--error': form.errors.email }">
                                            <i class="fas fa-envelope"></i>

                                            <input id="email" v-model="form.email" type="email" name="email"
                                                placeholder="exemple@email.com" autocomplete="email" />
                                        </div>

                                        <p v-if="form.errors.email" class="form-error">
                                            {{ form.errors.email }}
                                        </p>
                                    </div>

                                    <div class="form-group">
                                        <label for="phone">Téléphone</label>

                                        <div class="input-wrapper"
                                            :class="{ 'input-wrapper--error': form.errors.phone }">
                                            <i class="fas fa-phone"></i>

                                            <input id="phone" v-model="form.phone" type="tel" name="phone"
                                                placeholder="+223..." autocomplete="tel" />
                                        </div>

                                        <p v-if="form.errors.phone" class="form-error">
                                            {{ form.errors.phone }}
                                        </p>
                                    </div>

                                    <div class="form-group">
                                        <label for="user_type">
                                            Votre profil
                                        </label>

                                        <div class="input-wrapper"
                                            :class="{ 'input-wrapper--error': form.errors.user_type }">
                                            <i class="fas fa-users"></i>

                                            <select id="user_type" v-model="form.user_type" name="user_type">
                                                <option value="">
                                                    Sélectionnez votre profil
                                                </option>

                                                <option value="client">
                                                    Client
                                                </option>

                                                <option value="prestataire">
                                                    Prestataire
                                                </option>

                                                <option value="visitor">
                                                    Visiteur
                                                </option>

                                                <option value="partner">
                                                    Partenaire
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
                                            Sujet
                                            <span>*</span>
                                        </label>

                                        <div class="input-wrapper"
                                            :class="{ 'input-wrapper--error': form.errors.subject }">
                                            <i class="fas fa-tag"></i>

                                            <input id="subject" v-model="form.subject" type="text" name="subject"
                                                placeholder="Objet de votre demande" />
                                        </div>

                                        <p v-if="form.errors.subject" class="form-error">
                                            {{ form.errors.subject }}
                                        </p>
                                    </div>

                                    <div class="form-group form-group--full">
                                        <div class="form-label-line">
                                            <label for="message">
                                                Votre message
                                                <span>*</span>
                                            </label>

                                            <small>
                                                {{ messageLength }}/2000
                                            </small>
                                        </div>

                                        <div class="textarea-wrapper"
                                            :class="{ 'textarea-wrapper--error': form.errors.message }">
                                            <textarea id="message" v-model="form.message" name="message"
                                                maxlength="2000" rows="7"
                                                placeholder="Décrivez votre demande avec le plus de détails possible..."></textarea>
                                        </div>

                                        <p v-if="form.errors.message" class="form-error">
                                            {{ form.errors.message }}
                                        </p>
                                    </div>

                                    <div class="form-group form-group--full">
                                        <label class="checkbox-label">
                                            <input v-model="form.consent" type="checkbox" name="consent" />

                                            <span class="checkbox-custom">
                                                <i class="fas fa-check"></i>
                                            </span>

                                            <span class="checkbox-text">
                                                J’accepte que mes informations soient
                                                utilisées pour traiter ma demande.
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
                                        Envoi en cours...
                                    </template>

                                    <template v-else>
                                        Envoyer mon message
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
                            <span class="section-label">Besoin d’une réponse rapide ?</span>
                            <h2>Consultez les questions fréquentes</h2>
                            <p>
                                Retrouvez les réponses aux principales questions sur
                                les missions, les paiements, les comptes clients et
                                prestataires.
                            </p>
                        </div>

                        <Link href="/faq" class="faq-button">
                            Consulter la FAQ
                            <i class="fas fa-arrow-right"></i>
                        </Link>
                    </div>
                </div>
            </section>
        </main>
    </AppLayout>
</template>
