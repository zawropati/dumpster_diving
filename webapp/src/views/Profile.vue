<template>
<div class="flex" style="height:100%">
    <div class="profile-box">
        <div class="circle">
            <font-awesome-icon class="userIcon" color="#F5F5F5" icon="user" size="4x"/>
        </div>
        <div>
            <h2>{{user.firstName}} {{user.lastName}}</h2>
            <p>{{user.email}}</p>
        </div>
        <v-btn class="action-btn2" @click.prevent="logOut" ><font-awesome-icon  icon="sign-out-alt" size="lg"/> Log out</v-btn>
    </div>
    <div class="profile-content">
        <h2>My favourite locations</h2>
        <div class="flex">
            <div id="favourites-overview" v-for="location in locations" v-bind:key="location.location_fk">
                <v-card max-width="344" raised>
                    <v-card-title class="headline mb-1 fav-title">  
                        <font-awesome-icon id="heart" color="#D90368" icon="heart" size=lg />
                        {{location.city}}
                    </v-card-title>
                    <v-card-subtitle>
                        {{location.street}} {{location.zip}}
                    </v-card-subtitle>
                    <v-card-text>My note: {{location.note}}</v-card-text>
                </v-card>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import { HTTP } from './../config/http-request.js'

export default {
    name: 'Profile',
    data(){
        return {
        profileAPI: '/api-getUser.php',
        savedDumpstersApi: 'api-getSavedDumpsters.php',
        userData: {},
        user: {},
        locations: {}
        }
    },
    mounted(){
        this.getUserData()
        this.getSavedLocations()
    },
    methods: {
        getUserData(){
            var that = this
            HTTP.get(this.profileAPI + "?userID=" +this.$root.user)
                .then(function (response) {
                    that.userData = response.data.data
                    that.user = that.userData[0]
                })
                .catch(function (error) {
                    console.log(error);
                    alert(error);
            })
        },
        getSavedLocations(){
        var that = this
        HTTP.get(this.savedDumpstersApi + "?userID=" + this.$root.user)
            .then(response => {
                that.locations = response.data.data
            }).catch(error => {
                console.log(error)
            })     
        },
        logOut(){
            this.$root.user = ''
            localStorage.removeItem('userId')
            this.$router.push('/about')
        },
        assignId(){
            this.$root.user = this.userId
        }
    }
}
</script>

<style lang="sass" scoped>
.flex
    flex-wrap: wrap
#heart
    margin-right: 5px

</style>

