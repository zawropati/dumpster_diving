<template>
  <div>
    <div id="new-buttons" class="buttons">
      <v-btn-toggle rounded>
        <v-btn height='35px' active-class='view-active' @click="view = 'new'">New location</v-btn>
        <v-btn height='35px' active-class='view-active' @click="view = 'store'">See stores</v-btn>
      </v-btn-toggle>
    </div>
    <loader/>
    <stores-map v-if="view == 'store'"/>
    <new-location-map v-if="view == 'new'"/>
  </div>
</template>
<script>
import storesMap from './../components/storesMap.vue'
import newLocationMap from './../components/newLocationMap.vue'
import loader from  './../components/loader.vue'

export default {
  name: "NewLocation",
  components: {
    storesMap,
    loader,
    newLocationMap
  },
  data() {
    return {
      view: 'new'
    }
  }
};
</script>
<style  lang="sass">
.v-btn
  text-transform: none
  letter-spacing: 0

.v-btn-toggle > .v-btn
  background: #F5F5F5
  border-color: #61C67A !important

.v-btn-toggle > .v-btn.v-btn--active 
  border: 3px solid #61c67a !important
  background: white


</style>
