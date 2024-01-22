<template>
  <div id="app">
    <div v-if="mobile" id="navMobile">
      <router-link to="/about"><font-awesome-icon icon="home" size="2x"/></router-link> 
      <router-link id="map-link" to="/"><font-awesome-icon icon="map-marker-alt" size="2x"/></router-link> 
      <router-link to="/newLocation">
        <font-awesome-icon icon="plus-circle" size="2x"/>
      </router-link> 
      <router-link v-if="!$root.user" to="/login"><font-awesome-icon icon="user" size="2x"/></router-link> 
      <router-link v-if="$root.user" to="/profile"><font-awesome-icon icon="user" size="2x"/></router-link> 
    </div>
    <div v-else id="nav">
      <div class="logo flex">
        <svg id="logo" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 464.001 464.001" style="enable-background:new 0 0 464.001 464.001;" xml:space="preserve" width="100px" height="100px" class=""><g><path style="fill:#7AF482" d="M242.664,50.665L216.088,8.001h-37.432C161.51,7.998,145.62,16.994,136.8,31.697L112,72.001l88,56  L242.664,50.665z" data-original="#6A9923" class="active-path" data-old_color="#6A9923"/><path style="fill:#61C67A" d="M382.792,84.905L352,32.001c-7.836-14.813-23.242-24.057-40-24h-95.912l71.912,136l-40,24  l119.056,13.544L424,64.001L382.792,84.905z" data-original="#88B337" class="" data-old_color="#88B337"/><path style="fill:#7AF482" d="M432,176.001l-96,56l40,72h64l16.72-27c9.285-15.032,9.719-33.909,1.136-49.352L432,176.001z" data-original="#6A9923" class="active-path" data-old_color="#6A9923"/><path style="fill:#61C67A" d="M280,304.001v-40l-80,88l80,104v-32h64c16.853,0.411,32.432-8.936,40-24l56-96H280z" data-original="#88B337" class="" data-old_color="#88B337"/><path style="fill:#7AF482" d="M80,312.001l-19.48,44.896l24.344,42.168c9.03,15.283,25.386,24.738,43.136,24.936h32v-112H80z" data-original="#6A9923" class="active-path" data-old_color="#6A9923"/><path style="fill:#61C67A" d="M176,240.001l-48-104l-128,8l45.752,28.368L16,232.001c-8.276,14.93-8.276,33.07,0,48l44.512,76.896  l16.656-28.896l59.456-103.2L176,240.001z" data-original="#88B337" class="" data-old_color="#88B337"/></g> </svg>
        <h3 id="logo-text">Dumpie </h3>
      </div>
      <router-link id="map-link" to="/">Dumpster map</router-link> 
      <router-link to="/newLocation">+ Add dumpster</router-link> 
      <router-link to="/about">About</router-link> 
      <router-link  v-if="!$root.user" to="/signup">Create profile</router-link> 
      <router-link v-if="!$root.user" to="/login">Login</router-link> 
      <router-link v-if="$root.user" to="/profile">Profile</router-link> 
      <a id="log-out" v-if="$root.user" @click.prevent="logOut" >Log out</a>
    </div>
    <router-view/>
  </div>
</template>
<script>
import { HTTP } from './config/http-request.js'

export default {
  data() {
    return{
      logoutApi: '/api-logout.php',
      notificationApi: '/api-notification.php',
      mobile: false,
      newDumpsters: {},
      text: ''
    }
  },
  components: {
  },
  created(){
    this.$root.user = localStorage.getItem('userId')
    if(!sessionStorage.getItem('notificationRead')){
      this.setCookie()
    }
  },
  mounted(){
    this.checkMobile()
    this.checkNotificationPerm()
  },
  methods: {
    checkMobile(){
      var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent)
      if (isMobile) {
        this.mobile = true
      }
    },
    logOut(){
        this.$root.user = ''
        localStorage.removeItem('userId')
        this.$router.push('/about')
    },
    checkNotificationPerm(){
      if (!("Notification" in window)) {
          alert("This browser does not support desktop notification");
        }
        else if (Notification.permission === "granted") {
            console.log('Notifcation permission granted.')
        }
        else if (Notification.permission !== "denied") {
          Notification.requestPermission().then(function (permission) {
            if (permission === "granted") {
              console.log('Notifcation permission granted.')
            }
          })
        }
    },
    setCookie(){
      var cookie = this.$cookies.get("lastVisited")
      if(cookie == null){
        this.$cookies.set('lastVisited', new Date())
      }else{
        var date = this.$moment(cookie).utc().format('YYYY-MM-DD HH:mm:ss')
        this.getNotification(date)
      }
    },
    updateCookie(){
      this.$cookies.set('lastVisited', new Date())
      sessionStorage.setItem('notificationRead', true)
    },
    getNotification(date){
      var that = this
      HTTP.get(this.notificationApi + "?lastDate=" + date)
      .then(function (response) {
        that.newDumpsters = response.data.data[0]
        if(that.newDumpsters.new_dumpsters > 0){
          if(that.newDumpsters.new_dumpsters == 1 ){
            that.text = ' new dumpster was added 🔥'
          } else { 
            that.text = ' new dumpsters were added 🔥'
          }
          new Notification(that.newDumpsters.new_dumpsters + that.text, {
            icon: 'https://i.ibb.co/DgDw640/dustbin.png',
            body:  'Since your last visit!',
            })
        }
        that.updateCookie()
      })
      .catch(function (error) {
          console.log(error);
      })
    }   
  }
}
</script>

<style lang="sass">

body, html 
  height: 100%

.input
  margin: 0.35em 1em
  // display: block
  padding: 15px
  font-size: 14px
  border-radius: 4px
  width: 20em
  border: none
  -webkit-box-shadow: 5px 5px 15px 1px rgba(0,0,0,0.08)
  box-shadow: 5px 5px 15px 1px rgba(0,0,0,0.08)
  background: white

select
  margin-bottom: 0.5em
  display: block
  padding: 15px
  font-size: 14px
  border-radius: 4px
  width: 25em
  margin: 0 auto
  border: none
  -webkit-box-shadow: 5px 5px 15px 1px rgba(0,0,0,0.08)
  box-shadow: 5px 5px 15px 1px rgba(0,0,0,0.08)

.flex
  flex-direction: row 
  display: flex
  justify-content: flex-start

#logo
  width: 30px
  height: 30px
  margin: 0 5px

.button
  padding: 8px 18px
  background-color: #2986ce
  border-radius: 5px
  color: white
  font-size: 20px
  cursor: pointer
  border: none
  -webkit-box-shadow: 5px 5px 15px 1px rgba(0,0,0,0.08)
  box-shadow: 5px 5px 15px 1px rgba(0,0,0,0.08)

.button:hover
  color: #fff
  transition: 0.2s ease-in
  background-color: #2986ce

.map-container
  height:100vh
  width: 100%
  padding-top: 55px

::placeholder
  color: #565051

#home
  display: flex
  height: 100%

#app
  font-family: Avenir, Helvetica, Arial, sans-serif
  -webkit-font-smoothing: antialiased
  -moz-osx-font-smoothing: grayscale
  text-align: center
  color: #2c3e50
  height: 100%

@keyframes gradient 
  0% 
    background-position: 0% 50%
  50% 
    background-position: 100% 50%
  100% 
    background-position: 0% 50%
	
body
  background: #F5F5F5

.leaflet-container
  font: 14px Avenir, Helvetica, Arial, sans-serif !important

.swal2-title
  font-family: Avenir, Helvetica, Arial, sans-serif

#nav 
  display: flex
  padding: 5px 30px
  justify-content: flex-end
  width: 100%
  z-index: 10001
  position: absolute

#nav
  .flex
    padding: 8px

#nav 
  a
    font-weight: 500
    margin: 5px 10px
    text-decoration: none
    padding: 7px 15px 5px 15px
    height: 35px
    color: #131B23

#nav 
  a.router-link-exact-active 
    color: #61C67A
    font-weight: 600  

#map-link
  background-color: #61c67a
  background-image: linear-gradient(19deg, #61c67a 0%, #7af482 100%)
  border-radius: 10px
  color: white

#map-link.router-link-exact-active 
    color:  #131B23 !important

#map-link:hover
  background-image: linear-gradient(19deg, #7af482 0%, #61c67a 100%)
  transition: 2s ease-in

.swal2-container 
  z-index: 10002 !important

#mapButton
  position: absolute
  z-index: 10001
  border-radius: 5px
  padding: 10px 20px
  background: white
  margin-top: -5em
  font-weight: 700

#logo-text
  font-size: 22px

.signup,.signin 
  background: white
  width: 375px
  padding: 2em 0
  display: block
  margin: 0 auto
  border-radius: 5px
  // box-shadow: 0px 5px 5px -3px rgba(0, 0, 0, 0.2), 0px 8px 10px 1px rgba(0, 0, 0, 0.14), 0px 3px 14px 2px rgba(0, 0, 0, 0.12)

.sign-box
  border-top-right-radius: 100% 500%
  border-bottom-right-radius: 100% 500%
  background: white
  display: flex
  height: 100%
  flex-direction: column
  justify-content: center
  align-items: center
  width: 50%

.home-content
  display: flex
  height: 100%
  flex-direction: column
  justify-content: center
  align-items: center
  width: 50%

.profile-box
  border-top-right-radius: 100% 500%
  border-bottom-right-radius: 100% 500%
  background: white
  display: flex
  height: 100%
  flex-direction: column
  align-items: center
  width: 40%
  padding-top: 6em
  div, .v-btn
    margin: 10px 0

.profile-content
  width: 60%
  padding-top: 6em
  h2
    margin-bottom: 10px
  .flex
    justify-content: center

.circle
    width: 120px
    height: 120px
    background: #69d57c 
    -moz-border-radius: 70px 
    -webkit-border-radius: 70px 
    border-radius: 70px
    -webkit-box-shadow: 5px 5px 15px 1px rgba(0,0,0,0.2)
    box-shadow: 5px 5px 15px 1px rgba(0,0,0,0.2)
    svg
      margin-top: 25px

.singin-image
  padding-top: 5em
  width: 50%
  height: 100%
  flex-direction: column
  justify-content: center
  align-items: center
  div 
    justify-content: center
    svg
      margin: 0 20px

#dumpster-image
  width: 270px

#dumpster-icon
  width: 50px

.bold
  font-weight: 700

#details
  width: 400px
  background: #F5F5F5
  height: 100%
  position: absolute
  z-index: 9999
  right: 0

#add-section
  width: 400px
  position: absolute
  z-index: 9999
  right: 0 

.close-btn
  font-size: 12px
  float: right
  padding: 0

.buttons 
  position: absolute
  z-index: 9999
  left: 42%

#new-buttons
  margin-top: 80px

#dumpster-buttons 
  margin-top: 20px

.view-active
  background-color: white !important
  border: 3px solid #61c67a !important

.theme--light.v-btn-toggle:not(.v-btn-toggle--group) .v-btn.v-btn 
  border-color: #61c67a !important

#favourites-overview
  text-align: left
  padding: 0.5em 2em
  width: 300px
  
.fav-title
  font-weight: 600 !important

#profile
    height: 120px 
    width: 500px
    margin: 0 auto
    padding: 1.5em 2em
 
#profile-content
    width: 1200px
    margin: 0 auto

#log-out 
  cursor: pointer
  
.action-btn
  background-image: linear-gradient(19deg, #61c67a 0%, #7af482 100%) 

.action-btn2
  border: 2px solid #61C67A
  letter-spacing: 0 !important
  background: #F5F5F5

.review
  text-align: left
  padding: 0em 1em 1em 1em

.user-section
  padding-top: 6em

#closeDetails
  float: left
  margin-top: 10px

.details-top
  text-align: right
  padding: 20px
  border-bottom: 1px solid #e6e6e6

#oops-text
  width: 500px
  margin: 0 auto
  padding: 2em 

.swal2-styled.swal2-confirm
  font-family: Avenir, Helvetica, Arial, sans-serif
  background-color: #3181b8 !important

.leaflet-tooltip-pane
  h3
    font-weight: 500
    font-family: Avenir, Helvetica, Arial, sans-serif
    font-size: 14px

#list
  width: 500px
  text-align: left

#home
  svg
    margin: 15px

@media only screen and (min-device-width: 320px) and (max-device-width: 480px) and (-webkit-min-device-pixel-ratio: 2) 
  .signup, .signin
    width: 90%

  .sign-box
    width: 100%

  .singin-image
    display: none

  .buttons 
    position: absolute
    z-index: 9998
    left: 22%

  #new-buttons
    margin-top: 20px

  #details
    width: 100%

  #add-section
    width: 100%

  #home
    display: flex
    flex-direction: column
    height: 100%
  .map-container
    padding: 0
  .home-content
    width: 100%
    margin-bottom: 4em

  #navMobile
    background: white
    height: 50px
    padding: 8px 0
    overflow: hidden
    position: fixed
    bottom: 0
    width: 100%
    z-index: 10000

  #navMobile
    a
      color: #2c3e50
      font-weight: 600
      margin: 0 10px
      padding: 20px 20px
  
  .logo
    display: none
  
  #mapButton
    position: absolute
    z-index: 10000
    border-radius: 18px
    padding: 8px 16px
    background: white
    margin-top: -7.5em
    font-weight: 700
    margin-left: -5.5em
    box-shadow: 0 2px 5px -1px rgba(0, 0, 0, 0.3)

  #favourites-overview
      text-align: left
      padding: 0.5em 2em
      width: 100%

  #profile
      height: 120px 
      width: 100%
      margin: 0 auto
      padding: 1.5em 2em
  
  #profile-content
      width: 100%
      margin: 0 auto

  #oops-text
    width: 100%
    margin: 0 auto
    padding: 2em 

  #add-sign
    margin-top: 2px

  #list
    width: 300px
    text-align: left
  #home
    svg
      width: 50px
      height: 50px
      margin: 5px

  .circle
      width: 100px
      height: 100px
      svg
          margin-top: 15px

</style>