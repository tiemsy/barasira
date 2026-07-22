<template>
  <div class="map-section">
    <h2 :class="['section-title']">{{ $t('ui.map.title') }}</h2>

    <div v-html="svg" @click="handleClick"></div>

    <div v-if="selectedCity" class="services-list">
      <h3>{{ selectedCity }}</h3>

      <ul>
        <li v-for="service in servicesByCity[selectedCity]" :key="service">
          {{ service }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import svg from '/public/images/svg/mali-map.svg'

const selectedCity = ref(null)

const servicesByCity = {
  Bamako: ['Plomberie', 'Électricité', 'Transport'],
  Kayes: ['Transport', 'Maçonnerie'],
  Sikasso: ['Couture', 'Agriculture'],
  Ségou: ['Menuiserie', 'Plomberie'],
  Mopti: ['Pêche', 'Transport'],
  Tombouctou: ['Tourisme', 'Guides'],
  Gao: ['Logistique', 'Sécurité'],
}

function handleClick(event) {
  const cityGroup = event.target.closest('.city')
  if (!cityGroup) return

  selectedCity.value = cityGroup.dataset.city
}
</script>
