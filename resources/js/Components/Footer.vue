<template>
  <footer class="footer">
    <div class="container footer-inner">
      <p>© {{ new Date().getFullYear() }} {{ $t('footer.copyright') }}</p>
      <div class="footer-links">
        <Link href="#">{{ $t('footer.terms') }}</Link>
        <Link href="#">{{ $t('footer.privacy') }}</Link>
        <Link href="#">{{ $t('footer.contactUs') }}</Link>
      </div>
    </div>
  </footer>
</template>

<script setup>
    import { Link } from '@inertiajs/vue3'
</script>
