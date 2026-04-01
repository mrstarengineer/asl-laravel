<template>
  <div class="main-layout">
    <navbar />
    <Menu />
    <div class="container mt-4">
      <child />
    </div>
  </div>
</template>

<script>
import Navbar from '~/components/Navbar'
import Menu from '~/components/Menu'

export default {
  name: 'MainLayout',

  components: {
    Navbar,
    Menu
  }
}
</script>
