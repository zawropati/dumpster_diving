<template>
  <div>
    <loader/>
    <dumpsters-map/>
  </div>
</template>
<script>
import dumpstersMap from './../components/dumpstersMap.vue'
import loader from  './../components/loader.vue'

export default {
  name: "Dumpsters",
  components: {
    dumpstersMap,
    loader
  }
}
</script>
