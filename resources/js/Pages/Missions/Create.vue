<template>
  <AppLayout title="Créer une mission">
    <section class="mission-create-section">
      <div class="container">
        <div class="mission-card">
          <h1 class="mission-title">Créer une mission</h1>
          <p class="mission-subtitle">
            Décris ton besoin afin de recevoir des propositions adaptées.
          </p>

          <div v-if="errors.general" class="alert-error">
            {{ errors.general[0] }}
          </div>

          <form @submit.prevent="submit" class="mission-form">
            <div class="form-group">
              <label for="title">Titre de la mission</label>
              <input
                id="title"
                v-model="form.title"
                type="text"
                placeholder="Ex : Réparer une fuite d’eau"
              />
              <span v-if="errors.title" class="error">{{ errors.title[0] }}</span>
            </div>

            <div class="form-group">
              <label for="category">Catégorie</label>
              <select id="category" v-model="form.category_id">
                <option value="">Sélectionner une catégorie</option>
                <option
                  v-for="category in categories"
                  :key="category.id"
                  :value="category.id"
                >
                  {{ category.name }}
                </option>
              </select>
              <span v-if="errors.category_id" class="error">{{ errors.category_id[0] }}</span>
            </div>

            <div class="form-group">
              <label for="description">Description</label>
              <textarea
                id="description"
                v-model="form.description"
                rows="5"
                placeholder="Explique précisément ce que tu souhaites..."
              ></textarea>
              <span v-if="errors.description" class="error">{{ errors.description[0] }}</span>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="city">Ville</label>
                <input
                  id="city"
                  v-model="form.city"
                  type="text"
                  placeholder="Ex : Bamako"
                />
                <span v-if="errors.city" class="error">{{ errors.city[0] }}</span>
              </div>

              <div class="form-group">
                <label for="budget">Budget estimé</label>
                <input
                  id="budget"
                  v-model="form.budget"
                  type="number"
                  min="0"
                  placeholder="Ex : 25000"
                />
                <span v-if="errors.budget" class="error">{{ errors.budget[0] }}</span>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="deadline">Date souhaitée</label>
                <input
                  id="deadline"
                  v-model="form.deadline"
                  type="date"
                />
                <span v-if="errors.deadline" class="error">{{ errors.deadline[0] }}</span>
              </div>

              <div class="form-group">
                <label for="address">Adresse</label>
                <input
                  id="address"
                  v-model="form.address"
                  type="text"
                  placeholder="Adresse ou quartier"
                />
                <span v-if="errors.address" class="error">{{ errors.address[0] }}</span>
              </div>
            </div>

            <button type="submit" class="btn-submit" :disabled="loading">
              {{ loading ? 'Création en cours...' : 'Publier la mission' }}
            </button>
          </form>
        </div>
      </div>
    </section>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import AppLayout from '@/Layouts/AppLayout.vue'
import { api } from '@/lib/api'

const router = useRouter()

const loading = ref(false)
const errors = ref({})
const categories = ref([])

const form = ref({
  title: '',
  category_id: '',
  description: '',
  city: '',
  budget: '',
  deadline: '',
  address: '',
})

const fetchCategories = async () => {
  try {
    const res = await api.get('/service-categories')
    categories.value = res.data.data || res.data
  } catch (e) {
    console.error('Erreur catégories:', e.response?.data || e)
  }
}

const submit = async () => {
  errors.value = {}
  loading.value = true

  try {
    await api.post('/missions', form.value)
    router.push('/missions')
  } catch (e) {
    console.error('Erreur création mission:', e.response?.status, e.response?.data)

    if (e.response?.status === 422) {
      errors.value = e.response.data.errors || {}
    } else if (e.response?.status === 401) {
      errors.value.general = ['Vous devez être connecté pour créer une mission.']
      router.push('/login')
    } else if (e.response?.status === 403) {
      errors.value.general = ['Vous devez valider votre adresse email.']
      router.push('/email/verify')
    } else {
      errors.value.general = [
        e.response?.data?.message || 'Erreur lors de la création de la mission.'
      ]
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchCategories()
})
</script>

<style scoped>
.mission-create-section {
  padding: 60px 0;
  background: #f7f8fa;
  min-height: 100vh;
}

.container {
  width: 92%;
  max-width: 900px;
  margin: 0 auto;
}

.mission-card {
  background: #fff;
  padding: 35px;
  border-radius: 18px;
  box-shadow: 0 12px 35px rgba(0, 0, 0, 0.08);
}

.mission-title {
  font-size: 32px;
  font-weight: 800;
  margin-bottom: 8px;
  color: #111827;
}

.mission-subtitle {
  color: #6b7280;
  margin-bottom: 30px;
}

.mission-form {
  display: flex;
  flex-direction: column;
  gap: 22px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 18px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

label {
  font-weight: 700;
  margin-bottom: 8px;
  color: #374151;
}

input,
select,
textarea {
  border: 1px solid #d1d5db;
  border-radius: 10px;
  padding: 13px 15px;
  font-size: 15px;
  outline: none;
  background: #fff;
}

input:focus,
select:focus,
textarea:focus {
  border-color: #16a34a;
}

.error {
  color: #dc2626;
  font-size: 13px;
  margin-top: 6px;
}

.alert-error {
  background: #fee2e2;
  color: #991b1b;
  padding: 12px 15px;
  border-radius: 10px;
  margin-bottom: 20px;
}

.btn-submit {
  background: #16a34a;
  color: white;
  border: none;
  border-radius: 12px;
  padding: 15px;
  font-size: 16px;
  font-weight: 800;
  cursor: pointer;
}

.btn-submit:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.btn-submit:hover {
  background: #15803d;
}

@media (max-width: 768px) {
  .mission-card {
    padding: 24px;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .mission-title {
    font-size: 26px;
  }
}
</style>
