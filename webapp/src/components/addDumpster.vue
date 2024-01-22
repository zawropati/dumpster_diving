<template>
  <div  id="add-section">
        <div class="review">
          <div>
            <label class="label">What could you find there?</label>
            <div id="categories" class="flex">
              <div  class="flex" v-for="category in categories" v-bind:key="category.category" >
                <input @click="selectFood(category.id)" class="checkbox"  :value="category.id" type="checkbox" >
                <label  for="food"> {{ category.category }} </label><br>
              </div>
            </div>
          </div>
          <div>
            <label class="label">How would you rate the spot?</label> 
            <v-rating :color="color" half-increments v-model="score"/>
          </div>
          <div>
            <label class="label"></label>
            <v-textarea label="Any tips? General review on the spot" v-model="review"></v-textarea>
          </div>
            <v-btn class="action-btn" @click="postReview()">Add review</v-btn>
        </div>
  </div>
</template>

<script>
import { HTTP } from './../config/http-request.js' 

export default {
  name: 'addDumpster',
  props: {
      storeId: Number,
      hasDumpster: Boolean,
      storeName: String,
      storeAddress: String,
  },
  data() {
    return {
      categories: {},
      categoriesApi: 'api-getFoodCategories.php',
      addDumpsterAPI: 'api-addDumpster.php',
      addReviewAPI: 'api-addReview.php',
      categoryId: 2,
      review: '',
      score: null,
      array: [],
      color: '#F5A841'

    }
  },
  mounted() {
    this.getCategories()
  },
  methods: {
    closeModal () {
        this.$emit('closeButton', false)
    },
    selectFood(id){
      if(this.array.includes(id)){ 
        for( var i = 0; i < this.array.length; i++){ 
          if ( this.array[i] === id) { 
            this.array.splice(i, 1) 
          }
        }
      }else{
        this.array.push(id)
      }
    },
    getCategories(){
      HTTP.get(this.categoriesApi,{
      }).then(response => {
          this.categories = response.data.data
      })
        .catch(function (error) {
            alert(error);
        });
    },
    postReview(){
      var that = this
      if(this.hasDumpster == false){
        var json_arr = JSON.stringify(this.array);
        let formData = new FormData()
        formData.append('categories', json_arr)
        formData.append('score', parseInt(this.score))
        formData.append('review', this.review)
        formData.append('storeId', this.storeId)
        HTTP.post(this.addDumpsterAPI, formData)
            .then(function (response) {
              if(response.data.status == 0){
                that.$swal({
                  title: response.data.message,
                  icon: 'warning',
                })              
                }else{
                  that.$swal({
                    title: response.data.message,
                    icon: 'success',
                  }) 
                  that.closeModal()
              }
            })
            .catch(function (error) {
                console.log(error);
                alert(error);
            });
      }else{
        var json_arr2 = JSON.stringify(this.array);
        let formData = new FormData()
        formData.append('categories', json_arr2)
        formData.append('score', parseInt(this.score))
        formData.append('review', this.review)
        formData.append('storeId', this.storeId)
        HTTP.post(this.addReviewAPI, formData)
            .then(function (response) {
              if(response.data.status == 0){
                that.$swal({
                  title: response.data.message,
                  icon: 'warning',
                })
              }else{
                that.$swal({
                  title: response.data.message,
                  icon: 'success',
                }) 
                that.closeModal()
              }
            })
            .catch(function (error) {
                console.log(error);
                alert(error);
            });
      }
    }
  }
}
</script>
<style lang="sass">
#categories
  flex-wrap: wrap

.checkbox
  width: 10px

.review > div 
  margin: 20px 0

.label
  font-size: 1.2em
  font-weight: 600
  margin-bottom: 0.5em

#storeName
  text-align: left
  font-weight: normal
  text-decoration: underline

</style>
