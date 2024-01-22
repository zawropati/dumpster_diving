<template>
  <div class="map-container">
  <div v-if="addNewClicked">
    <div class="new-box" id="details">
        <button  id="closeDetails" class="close-btn" @click="addNewClicked = false">Close</button>
        <h3 class="title">Add new dumpster</h3>
        <div>
            <v-text-field label="Store name" v-model="storeName" hide-details="auto"></v-text-field>
            <v-text-field label="Address" v-model="address" hide-details="auto"></v-text-field>
        <div class="flex">
            <v-text-field label="City" v-model="city" hide-details="auto"></v-text-field>
            <v-text-field label="Post code" v-model="zip" type="number" hide-details="auto"></v-text-field>
        </div>
        <div class="middle">
            <label>How would you rate the spot?</label> 
            <v-rating :color="color" half-increments v-model="score"/>
        </div>
        <div class="middle">
            <label>What could you find there?</label>
            <div id="categories" class="flex">
                <div id="category" class="flex" v-for="category in categories" v-bind:key="category.category" >
                    <input @click="selectFood(category.id)" class="checkbox"  :value="category.id" type="checkbox" >
                    <label  for="food"> {{ category.category }} </label><br>
                </div>
            </div>
        </div>
        <v-textarea id="review-text" label="Any tips? General review on the spot" v-model="review"></v-textarea>
        <v-btn class="action-btn" @click="addNewLocation()">Post</v-btn>
        </div>
    </div>
  </div>
    <l-map
      :zoom="zoom"
      :center="center"
      :options="mapOptions"
      style="height: 100%"
      @update:zoom="zoomUpdate"
      @click="addMarker"
    >
    <l-tile-layer
    :url="url"
    :attribution="attribution"
    />
        <l-marker
        ref="marker2"
        v-if='newMarker == null'
        :lat-lng='center'
        :icon="icon"
        >
            <l-tooltip v-if='newMarker == null'>
                <h3>
                    Click on the map to add new location!
                </h3> 
            </l-tooltip>
        </l-marker>
        <l-marker
        :icon="icon"
        ref="marker"
        v-if='mapClicked'
        :lat-lng='newMarker'
        @click="addNewLocation(newMarker)"
        >
        <div id="popup">
            <l-popup  v-if='mapClicked'>
                <v-btn class="action-btn2" @click="addNewClicked = true"> + Add</v-btn>
            </l-popup>
        </div>
        </l-marker>
    </l-map>
  </div>
</template>
<script>
import { HTTP } from './../config/http-request.js' 
import { latLng } from "leaflet"
import { LMap, LTileLayer, LMarker, LPopup, LTooltip } from "vue2-leaflet"
import L from 'leaflet'

export default {
  name: "newLocationMap",
  components: {
    LMap,
    LTileLayer,
    LMarker,
    LPopup,
    LTooltip
  },
  data() {
    return {
        zoom: 12  ,
        center: latLng(55.6761, 12.5683),
        url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        attribution:
            '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
        currentZoom: 10,
        review: '',
        score: null,
        storeName: '',
        city: '',
        zip: '',
        address:'',
        mapOptions: {
            zoomSnap: 0.5,
        },
        icon: L.icon({
            iconUrl: 'https://i.ibb.co/hL2mS5V/pin-3.png',
            iconSize:[40, 40],
            iconAnchor: [20, 0],
        }),
        newMarker: null,
        addNewClicked: false,
        mapClicked: false,
        color: '#F5A841',      
        categoriesApi: 'api-getFoodCategories.php',
        newLocationApi: 'api-addNewLocation.php',
        array: []
    };
  },
  mounted() {
        this.getCategories()
        setTimeout(() => {
          this.open()
        }, 1);
  },
  methods: {
    open(){
          this.$refs.marker2.mapObject.openTooltip()
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
    addMarker(event) {
        this.mapClicked = true
        this.newMarker = event.latlng
        setTimeout(() => {
            this.$refs.marker.mapObject.openPopup()
        }, 1);
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
    addNewLocation(){
        var json_arr = JSON.stringify(this.array);
        let formData = new FormData()
        formData.append('categories', json_arr)
        formData.append('score', parseInt(this.score))
        formData.append('review', this.review)
        formData.append('storeName', this.storeName)
        formData.append('address', this.address)
        formData.append('city', this.city)
        formData.append('zipcode', this.zip)
        formData.append('lat', this.newMarker.lat)
        formData.append('lng', this.newMarker.lng)
        var that = this
        HTTP.post(this.newLocationApi, formData)
        .then(response => {
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
            that.addNewClicked = false
        }
        })
        .catch(function (error) {
            alert(error);
        });
    },
    zoomUpdate(zoom) {
      this.currentZoom = zoom;
    }
  }
}
</script>
<style lang="sass">
.title
    display: inline
    margin-left: 30px 
    
.v-input__control
    margin: 0 15px 

.review
    text-align: center

.checkbox
    border-radius: 50%
    cursor: pointer
    transition: inherit
    width: 34px
    margin-left: 7px
    height: 17px

.new-box
    padding: 20px

#categories
    margin-top: 20px 

.v-rating
    margin-top: 10px 

#category
    margin: 5px

.v-textarea textarea
    line-height: 1.5rem

.v-textarea 
    padding-top: 0px

.middle
    margin: 20px 0 !important

.leaflet-popup-content-wrapper
    background: none !important
    box-shadow: none !important

</style>
