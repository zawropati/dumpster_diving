<template>
  <div class="map-container">
    <div v-if="showReview == true" id="details">
      <div class="details-top">
        <button id="closeDetails" class="close-btn" @click="showReview = false">Close </button>
        <h2>{{storeName}} {{storeAddress}}</h2>
      </div>
      <add-dumpster @closeButton="closeDetails" :storeName="storeName" :storeAddress="storeAddress" :hasDumpster="false" :storeId="storeId"/>
    </div>
    <l-map
      v-if="showMap"
      :zoom="zoom"
      :center="center"
      :options="mapOptions"
      style="height: 100%"
      @update:zoom="zoomUpdate"
    >
      <l-tile-layer
        :url="url"
        :attribution="attribution"
      />
      
      <l-marker :icon="icon" @click="addReview(marker)" v-for="marker in markers" :lat-lng="marker.latlng" v-bind:key="marker.id">
        <l-tooltip :options="{ permanent: false, interactive: true }">
          <div>
            {{marker.brand}} address: {{ marker.street }}
          </div>
        </l-tooltip>
      </l-marker>
    </l-map>
  </div>
</template>
<script>
import { latLng } from "leaflet";
import { LMap, LTileLayer, LMarker, LTooltip } from "vue2-leaflet";
import L from 'leaflet';
import { HTTP } from './../config/http-request.js'
import addDumpster from './../components/addDumpster.vue'

export default {
  name: "storesMap",
  components: {
    addDumpster,
    LMap,
    LTileLayer,
    LMarker,
    // LPopup,
    LTooltip
  },
  data() {
    return {
      zoom: 12  ,
      center: latLng(55.6761, 12.5683),
      url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
      attribution:
        '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
      withPopup:  latLng(55.6761, 12.5683),
      withTooltip:  latLng(55.6761, 12.5683),
      currentZoom: 10,
      currentCenter: latLng(55.6761, 12.5683),
      showReview: false,
      mapOptions: {
        zoomSnap: 0.5
      },
      icon: L.icon({
        iconUrl: 'https://i.ibb.co/Jp8FCPC/pin-4.png',
        iconSize:[40, 40],
        iconAnchor: [30, 30],
      }),
      showMap: false,
      stores: {},
      reviewedStores: {},
      latLngs: [],
      markers: [],
      place: null,
      details: {},
      postAPI:'api-savedata.php',
      storesAPI: 'api-getStores.php',
      dumpstersApi: 'api-getDumpsters.php',
      detailsAPI: 'api-getDumpsterDetails.php',
      storeName: '',
      storeAddress: '',
      storeId: null,
      showAddReview: false,
    };
  },
  mounted() {
    this.getStores()
  },
  methods: {
    closeDetails(value){
      this.showReview = value
    },
    getStores(){
      this.markers = []
      HTTP.get(this.storesAPI,{
      }).then(response => {
          this.stores = response.data.data;
            this.stores.forEach(store => {
              this.markers.push({
                id: store.id,
                latlng: latLng(store.lat, store.lng),
                brand: store.name,
                city: store.city,
                street: store.street,
                zip: store.zip
              })
          });
            this.showMap = true
        }).catch(error => {
          console.log(error)
        })
    },
    getStoresFromExternal(){
      this.$http.get("https://api.sallinggroup.com/v2/stores?geo=55.680572,12.585894&radius=10&page=1&per_page=10000",{
        headers: {
          Authorization: 'Bearer d5b551b1-cfcc-4638-9de5-5f6588aca769'
        }
      }).then(response => {
          this.stores = response.body;
            this.stores.forEach(store => {
              this.markers.push({
                id: store.id,
                latlng: latLng(store.coordinates[1], store.coordinates[0]),
                brand: store.brand,
                street: store.address.street
              })
          });
            this.showMap = true
        }).catch(error => {
          console.log(error)
        })
    },


    getStoresByDistrict(place){
      this.markers = []
      this.$http.get('https://api.sallinggroup.com/v2/stores?city=' + place +  '&page=1&per_page=10000',{
          headers: {
          // "Access-Control-Allow-Origin": "*",
          Authorization: 'Bearer d5b551b1-cfcc-4638-9de5-5f6588aca769'
        }
      }).then(response => {
            this.stores = response.body;
              this.stores.forEach(store => {
                this.markers.push({
                  id: store.id,
                  latlng: latLng(store.coordinates[1], store.coordinates[0]),
                  brand: store.brand,
                  street: store.address.street
                })
            });
            this.showMap = true
          }, response => {
            console.log(response)
          });
    },
    postStores(){
      var dataToSave = []
      this.stores.forEach(store => {
        dataToSave.push({
          name: store.brand,
          lat: store.coordinates[1], 
          lng: store.coordinates[0],
          city: store.address.city,
          street: store.address.street,
          zip: store.address.zip
        })
      });
       let headers = {
        'Content-Type': 'application/json;charset=utf-8'
      };
      HTTP.post(this.postAPI,{
          'stores' : dataToSave,
        }, {headers} ).then( {
        }).catch(error => {
          console.log(error)
        })
    },
    zoomUpdate(zoom) {
      this.currentZoom = zoom;
    },
    addReview(marker) {
      this.storeName = marker.brand
      this.storeAddress = marker.street
      this.storeId = parseInt(marker.id)
      this.showAddReview = true
      this.showReview = true
    }
  }
}
</script>
<style lang="sass">

</style>
