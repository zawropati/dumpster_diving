<template>
  <v-container id="not-found">
    <v-row class="text-center">
      <v-col cols="12">
            <h1> <font-awesome-icon id="warning" color="#69D57C" icon="exclamation-circle" size="x" />Ooops!</h1>
            <h2 id="oops-text">Seems like the route doesn't exist or you do not have permission to access it!</h2>
            <v-btn class="action-btn2" @click="goTo()">Go back to dumpster map </v-btn>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  export default {
    name: 'notFound',
    methods: {
        goTo(){
            this.$router.push('/')
        }
    }
  }
</script>
<style lang="sass" scoped>
#not-found
  padding-top: 80px 

#warning
  margin: 0 5px
</style>
