<template>
<div id="add-section">
    <div class="review">
        <div>
        <h2>Add to favourites</h2>
        <p class="italic">Psss! Only you will be able to see it.</p>
        <v-textarea label="Write personal note..." v-model="note"/>
        <v-btn class="action-btn" @click="addToFavourite()"><font-awesome-icon id="heart" color="#D90368" icon="heart" size=lg /> Add</v-btn>
        </div>
    </div>
</div>
</template>

<script>
import { HTTP } from './../config/http-request.js' 

export default {
  name: 'addToFavourites',
  props: {
      dumpsterId: Number,
  },
  data() {
    return {
      addToFavAPI: 'api-addToFavourites.php',
      note: '',
    }
  },
  mounted() {
  },
  methods: {
    closeModal () {
        this.$emit('closeButton', false)
    },
    addToFavourite(){
        var that = this 
        let formData = new FormData()
        formData.append('note', this.note)
        formData.append('dumpsterId', this.dumpsterId)
        formData.append('userId', this.$root.user)
        HTTP.post(this.addToFavAPI, formData)
            .then(function (response) {
                that.$swal(response.data.message)
                that.$emit('closeButton', false)
            })
            .catch(function (error) {
                console.log(error);
                alert(error);
            });
    }
  }
}
</script>
<style scoped lang="sass">
.checkbox
  width: 10px

</style>
