<template>
  <div class="map-container">
    <div id="dumpster-buttons" class="buttons" v-if="$root.user">
      <v-btn-toggle rounded>
        <v-btn height='35px' active-class='view-active' @click="getDumpsters(),  detailsClicked = false">All dumpsters</v-btn>
        <v-btn height='35px' active-class='view-active'  @click="favouriteFilter(), detailsClicked = false">Your favourites</v-btn>
      </v-btn-toggle>
    </div> 
    <transition name="list">
    <div v-if="detailsClicked" id="details">  
      <div class="details-top">
        <button id="closeDetails" class="close-btn" @click="detailsClicked = false">Close</button>
        <div class='flex'>
        <h2>
          {{marker.brand}} dumpster
        </h2>
          <img id="dumpster-icon" src="https://i.ibb.co/DgDw640/dustbin.png"/>
        </div>
        <div class="flex">
        <h4> {{ parseFloat(marker.avgScore).toFixed(1) }}</h4>
        <v-rating :color="color" half-increments :readonly="true" v-model="marker.avgScore"/>
      </div>
        <p>{{marker.street}}, {{marker.city}}</p>
      </div>
      <div v-if="showReview == false && favouriteClicked == false" id="details-content">
        <div id="food-category"> 
          <p>
          <font-awesome-icon icon="carrot" size="lg" />
            Most people could find</p>
          <h4> {{mostFrequent.category}} </h4>
        </div>
        <div id="details-btns" class="flex">
          <v-btn class="action-btn2" @click="addReview()">Add review </v-btn>
          <div id="fav-group" v-if="$root.user">
            <v-btn v-if="marker.is_saved == null " class="add-btn" @click="favouriteClicked = true">+ Add to favourite</v-btn>
            <v-btn v-else id="saved"> <font-awesome-icon id="heart" color="#D90368" icon="heart" size=lg />Added to favourites</v-btn>
          </div>
        </div>
        <div v-if="marker.is_saved ==1 && mapFiltered == true">
          <h2>My personal note</h2>
          <p>{{marker.note}}</p>
        </div>
        <div v-else id="reviews-box">
        <div v-for="review in reviews" v-bind:key="review.id" id="reviews">
          <div class="flex">
              <span> {{ review.score }}</span>  <v-rating :color="color" :x-small="true" half-increments :readonly="true" v-model="review.score"/>
          </div>
          <p>
              {{ review.review }}
          </p>
          <p class="italic" v-if="review.review == ''"> No review</p>
          <p id="review-date">{{review.date}}</p>
        </div>
        </div>
      </div>
      <div id="details-content" v-if="showReview == true" >
        <v-btn class="action-btn2" @click="showReview = false">Go back </v-btn>
        <add-dumpster @closeButton="closeDetails" :showAddReview="showAddReview" :storeName="storeName" :storeAddress="storeAddress" :hasDumpster="true" :storeId="storeId"/>
      </div>
      <div v-if="favouriteClicked == true" id="details-content">
        <v-btn class="action-btn2" @click="favouriteClicked = false">Go back</v-btn>
        <add-to-favourites @closeButton="onClickChild" :dumpsterId="marker.id"/>
      </div>
    </div>
    </transition>
    <l-map
      v-if="showMap"
      :zoom="zoom"
      :center="center"
      :options="mapOptions"
      style="height: 100vh"
      @update:zoom="zoomUpdate"
    >
      <l-tile-layer
        :url="url"
        :attribution="attribution"
      />
      <l-marker 
        :icon="icon" 
        @click="seeDetails(marker)"  
        v-for="marker in markers" 
        :lat-lng="marker.latlng" 
        v-bind:key="marker.id">
      </l-marker>
      <l-marker
        v-if="locationDenied == false"
        :lat-lng="userPosition.latLng" 
        :icon="userIcon"
      >
      </l-marker>
    </l-map> 
  </div>
</template>
<script>
import { latLng } from "leaflet";
import { LMap, LTileLayer, LMarker } from "vue2-leaflet";
import { HTTP } from './../config/http-request.js'
import L from 'leaflet';
import addDumpster from './../components/addDumpster.vue'
import addToFavourites from './../components/addToFavourites.vue'

export default {
  name: "dumpstersMap",
  components: {
    addDumpster,
    LMap,
    LTileLayer,
    LMarker,
    addToFavourites
  },
  data() {
    return {
      zoom: 12  ,
      center: latLng(55.6761, 12.5683),
      url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
      attribution:
        '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
      currentZoom: 10,
      currentCenter: latLng(55.6761, 12.5683),
      showReview: false,
      mapOptions: {
        zoomSnap: 0.5
      },
      icon: L.icon({
        iconUrl: 'https://i.ibb.co/hL2mS5V/pin-3.png',
        iconSize:[40, 40],
        iconAnchor: [30, 30],
      }),
      userIcon: L.icon({
        iconUrl: 'https://i.ibb.co/tZWhWWy/a40e-K6b-Yz.png',
        iconSize:[60, 60],
        iconAnchor: [40, 40],
        className: 'userLocation' 
      }),
      showMap: false,
      stores: {},
      marker: {},
      markers: [],
      reviews: [],
      locationDenied: false,
      details: {},
      foods: {},
      mostFrequent: {},
      dumpstersApi: 'api-getDumpsters.php',
      savedDumpstersApi: 'api-getSavedDumpsters.php',
      detailsAPI: 'api-getDumpsterDetails.php',
      storeName: '',
      storeAddress: '',
      storeId: null,
      detailsClicked: false,
      favouriteClicked: false,
      mapFiltered: false,
      color: '#F5A841',
      limit: 5,
      userPosition: {},
      show: false,
      showAddReview: true
    }
  },
  computed:{
    computedObj(){
      return this.limit ? this.reviews.slice(0,this.limit) : this.reviews
    }
  },
  beforeMount(){
    this.getLocation()
  },
  mounted() {
    this.getDumpsters()
  },
  methods: {
    closeDetails(value){
      this.showReview = value
      this.seeDetails(this.marker)
    },
    onClickChild (value) {
      this.favouriteClicked = value
      this.seeDetails(this.marker)
    },
    getLocation() {
      if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition(position => {
          this.userPosition = {}
          this.userPosition = {
            latLng: latLng(position.coords.latitude, position.coords.longitude)
          }
          this.showMap = true
        }, err => {
          console.log(err.message)
          this.locationDenied = true
          this.showMap = true
        })
      }else{
        this.getDumpsters()
      }
    },
    favouriteFilter(){
      var that = this
      HTTP.get(this.savedDumpstersApi + "?userID=" + this.$root.user)
      .then(response => {
          that.markers = []
          that.stores = response.data.data;
          that.stores.forEach(store => {
            that.markers.push({
                id: store.location_fk,
                latlng: latLng(store.lat, store.lng),
                brand: store.name,
                city: store.city,
                street: store.street,
                zip: store.zip,
                avgScore: store.avgScore,
                is_saved: true,
                note: store.note
              })
              this.mapFiltered = true
          })
        }).catch(error => {
          console.log(error)
        })     
    },
    getDumpsters(){
      this.markers = []
      var that = this
      HTTP.get(this.dumpstersApi + "?userId=" + this.$root.user)
      .then(response => {
          that.stores = response.data.data;
          that.stores.forEach(store => {
            that.markers.push({
                id: store.dumpster_fk,
                latlng: latLng(store.lat, store.lng),
                brand: store.name,
                city: store.city,
                street: store.street,
                zip: store.zip,
                avgScore: store.avgScore,
                date: store.date,
                dumpster: true,
                is_saved: store.is_saved
              })
              this.mapFiltered = false
              this.showMap = true
          })
        }).catch(error => {
          console.log(error)
        })
    },
    zoomUpdate(zoom) {
      this.currentZoom = zoom;
    },
    addReview() {
        this.storeName = this.marker.brand
        this.storeAddress = this.marker.street
        this.storeId = parseInt(this.marker.id)
        this.showReview = true
    },
    seeDetails(marker){
      this.limit = 5
      this.detailsClicked = true
      this.showReview = false
      this.favouriteClicked = false
      this.marker = marker
      HTTP.get(this.detailsAPI + "?dumpsterId=" + marker.id,{
      }).then(response => {
            this.details = response.data.data
            this.reviews = this.details.reviews
            this.foods = response.data.data.food
            if(this.foods.length !== 0){
              var frequency = Math.max.apply(Math, this.foods.map(function(o) { return parseInt(o.frequency) })) 
              this.mostFrequent = this.foods.find(function(o){ return parseInt(o.frequency) == frequency})
            }else{
              this.mostFrequent.category = "no data so far :("
            }
        }).catch(error => {
          console.log(error)
        })
    } 
  }
};
</script>
<style lang="sass">
.userLocation
  animation: fade 1s infinite alternate
  position: relative
  border-radius: 50%

@keyframes fade 
  from
    opacity: 0.5


.details-top .flex
  margin: 10px 0

#details-content
  padding: 20px

#heart
  margin: 5px

#filter
  position: absolute
  z-index: 10000
  background: rgba(255,255,255,0.75)
  padding: 5px
  border-radius: 5px

.add-btn
  background: green
  border-radius: 5px
  border-bottom: none
  color: white
  font-size: 14px
  display: block
  margin: 0 auto
  margin-left: 20px
  
#saved
  pointer-events: none
  box-shadow: none
  border: none

.details-top .flex, #reviews .flex
  align-items: center
  justify-content: flex-end

#reviews
  text-align: right
  border-top: 1px solid #e6e6e6

#reviews-box
  height: 320px
  overflow-y: auto

#review-date
  font-size: 12px

#food-category
  padding: 10px
  background-color: #61c67a
  background-image: linear-gradient(19deg, #61c67a 0%, #7af482 100%)
  border-radius: 5px
  margin-bottom: 20px

#details-btns
  margin: 20px 0
  
.italic
  font-style: italic

.leaflet-popup-content-wrapper
  border-radius: 10px
  box-shadow: none
  padding: 1px 10px

.leaflet-container
  font: 14px Avenir, Helvetica, Arial, sans-serif !important

.list-enter,
.list-leave-to 
  margin-right: -400px

.list-enter-active,
.list-leave-active 
  transition: margin-right .2s ease-out

.buttons
  margin-top: 20px

</style>
