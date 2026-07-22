<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md px-4">
      <!-- Logo / Nom -->
      <div class="text-center mb-6">
        <Link href="/" class="text-3xl font-bold text-teal-700">
          Barasira
        </Link>
        <p class="text-sm text-gray-500">
          {{ $t('footer.description') }}
        </p>
      </div>

      <!-- Contenu (Login / Register) -->
      <slot />
    </div>
  </div>
</template>

<script setup>
    import { Link } from '@inertiajs/vue3'
</script>
