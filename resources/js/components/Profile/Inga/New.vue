<template>
    <div class="newInga">
        <form autocomplete="off" @submit.prevent="newIngaSubmit">
            <div class="textareaContainer">
                <!--@blur.native="onBlurTextarea"-->
                <textarea-autosize
                    placeholder="Inga Paylaş?"
                    ref="newingatexarea"
                    v-model="message"
                    :min-height="55"
                    :max-height="150"
                    @input="CharacterCount"
                    @focus.native="setFocus"
                    @blur.native="disableFocus"
                ></textarea-autosize>
                <!--@keyup.enter.native="newIngaSubmit"-->
            </div>
            <div class="videoUrl" v-if="ingaVideoSelect">
                <input type="text" placeholder="Youtube, Vimeo veya DailyMotion linkini yapıştırınız." @keyup="videoDetect" v-model="ingaVideoUrl">
            </div>
            <div class="addonsInga" v-if="addonsView">
                <div class="maxCharacter addonBox" data-toggle="tooltip" data-placement="bottom" title="Kalan Inga Karakter Sayısı">
                    {{remainingChar}}
                </div>
                <input type="file" @change="onFileChanged" id="ingaFileInput" class="d-none" accept="image/jpeg,image/gif,image/png">
                <div class="addonBox" data-toggle="tooltip" data-placement="bottom" title="Resim Yükle" @click="imageUpload()"  v-bind:class="{ active : ingaImageUpload}" v-if="!ingaVideoSelect">
                    <img src="/svg/picture.svg">
                </div>
                <div class="addonBox" data-toggle="tooltip" data-placement="bottom" title="Lokasyon Paylaş" v-bind:class="{ active : shareLocation}" @click="getLocation()">
                    <img src="/svg/location.svg">
                </div>
                <div class="addonBox" data-toggle="tooltip" data-placement="bottom" title="Video Ekle" v-if="!ingaImageUpload" @click="videoSelect()" v-bind:class="{ active : ingaVideoSelect}">
                    <img src="/svg/video.svg">
                </div>
                <button type="submit" class="btn-primary">
                    Paylaş
                </button>
                <div class="clearfix"></div>
            </div>
            <div class="inga preview" v-if="message.length>0">
                <div class="avatar">
                    <img v-if="profile.profile_image_upload" v-bind:src="'/uploads/avatars/thumbs/'+profile.profile_image" />
                    <img v-else-if="profile.profile_image" v-bind:src="profile.profile_image">
                    <img v-else src="/images/logo.png" />
                </div>
                <div class="ingaContent">
                    <div class="username">
                        <b>{{profile.name}}</b>
                        <span>@{{profile.username}}</span>
                        <label>şimdi</label>
                    </div>
                    <div class="ingaText">
                        <nl2br tag="div" :text="message"></nl2br>
                    </div>
                </div>
                <div class="postImage" v-if="ingaImageUpload">
                    <img v-bind:src="ingaImageUrl" />
                </div>
                <div v-if="shareLocation" class="location">
                    <img src="/svg/black-pin.svg" alt="">
                    {{ locationAddress }}
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    import VueTextareaAutosize from 'vue-textarea-autosize'
    Vue.use(VueTextareaAutosize);
    export default {
        name: "New",
        data() {
            return {
                profile : [],
                maxChar : 500,
                remainingChar : 500,
                message : '',
                addonsView : false,
                shareLocation : false,
                locationAddress : '',
                locationLatitude : '',
                locationLongitude : '',
                ingaVideoDetect : false,
                ingaVideoSelect : false,
                ingaVideoId : '',
                ingaVideoUrl : '',
                ingaVideoWebsite : '',
                ingaVideoThumbnail : '',
                selectedFile: null,
                ingaImageUpload : false,
                ingaImageUrl : '',
            }
        },
        mounted() {
          this.getProfileInformation();
        },
        methods: {
            newIngaSubmit() {
                this.$parent.$parent.isLoading = true;
                if(this.message.length>0 && this.message.length<=500) {
                    if (this.ingaVideoSelect) {
                        if (!this.ingaVideoDetect) {
                            this.$snotify.error('Video bulunamadı lütfen video linkini kontrol ediniz!');
                            this.$parent.$parent.isLoading = false;
                            return false;
                        }
                    }

                    var app = this;
                    axios.post(
                        '/profile/new-inga',
                        {
                            message : app.message,
                            image_upload : app.ingaImageUpload,
                            image : app.ingaImageUrl,
                            sharelocation : app.shareLocation,
                            address : app.locationAddress,
                            latitude : app.locationLatitude,
                            longitude : app.locationLongitude,
                            video_select : app.ingaVideoSelect,
                            video_detect : app.ingaVideoDetect,
                            video_id : app.ingaVideoId,
                            video_thumbnail : app.ingaVideoThumbnail,
                            video_website : app.ingaVideoWebsite,
                            video_url : app.ingaVideoUrl
                        }
                    ).then(response=>{
                        if(response.status==200) {
                            if(response.data.success) {
                                this.$parent.$parent.isLoading = false;
                                app.message = '';
                                app.addonsView = false;
                                app.ingaVideoDetect = false;
                                app.ingaVideoSelect = false;
                                app.ingaVideoId = '';
                                app.ingaVideoUrl = '';
                                app.ingaVideoWebsite = '';
                                app.ingaVideoThumbnail = '';
                                app.selectedFile = null;
                                app.ingaImageUpload = false;
                                app.ingaImageUrl = '';
                                app.$parent.$refs.myprofileingas.getLastMyInga(response.data.post);
                            } else {
                                this.$parent.$parent.isLoading = false;
                                this.$parent.$parent.showErrors(response.data.errors);
                            }
                        }
                    }).catch(error=>{
                        this.$parent.$parent.isLoading = false;
                        this.$snotify.error(error);
                    })
                } else if (this.message.length>500) {
                    this.$parent.$parent.isLoading = false;
                    this.$snotify.error('Maximum 500 karakter inga yazabilirsiniz...');
                } else {
                    this.$parent.$parent.isLoading = false;
                    this.$snotify.error('Lütfen bir mesaj yazınız...');
                }
            },
            CharacterCount() {
                this.remainingChar = this.maxChar-this.message.length
            },
            setFocus: function()
            {
                this.addonsView = true;
            },
            disableFocus: function()
            {
               // if(this.message.length==0) {
               //     this.addonsView = false
               // }
            },
            videoDetect() {
                if (this.ingaVideoUrl.length>0) {
                    var _this = this;
                    axios.get('/profile/new-inga/detect-video/?url='+this.ingaVideoUrl)
                    .then(response => {
                        if(response.data.id) {
                            _this.ingaVideoDetect = true;
                            _this.ingaVideoWebsite = response.data.website;
                            _this.ingaVideoId = response.data.id;
                            _this.ingaVideoThumbnail = response.data.thumbnail;
                        }
                    })
                    .catch(error=>{

                    });
                }
            },
            videoSelect() {
                if(this.ingaVideoSelect) {
                    this.ingaVideoSelect = false;
                    this.ingaVideoDetect = false;
                    this.ingaVideoWebsite = '';
                    this.ingaVideoId = '';
                    this.ingaVideoThumbnail = '';
                } else {
                    this.ingaVideoSelect = true;
                }
            },
            imageUpload() {
               if (this.ingaImageUpload == true) {
                   this.ingaImageUpload = false;
                   this.ingaImageUrl = '';
               } else {
                   document.getElementById('ingaFileInput').click();
               }
            },
            onFileChanged (event) {
                this.selectedFile = event.target.files[0];
                this.onUpload();
            },
            onUpload() {
                this.$parent.$parent.isLoading = true;
                var app = this;
                const formData = new FormData();
                formData.append('image', this.selectedFile, this.selectedFile.name);
                axios.post(
                    '/profile/new-inga/upload-image',formData
                ).then(response=>{
                    if(response.status==200) {
                        if(response.data.success) {
                            app.ingaImageUpload = true;
                            app.ingaImageUrl = response.data.url;
                            this.$parent.$parent.isLoading = false;
                        } else {
                            app.ingaImageUpload = false;
                            this.$parent.$parent.isLoading = false;
                            this.$snotify.error(response.data.errors);
                        }
                    }
                }).catch(error=>{
                    app.ingaImageUpload = false;
                    this.$parent.$parent.isLoading = false;
                    alert(error);
                })
            },
            getLocation() {
                this.shareLocation = !this.shareLocation;
                if (this.shareLocation) {
                    this.locationAddress = '';
                    this.locationLatitude = '';
                    this.locationLongitude = '';
                    if (this.$cookies.get('currentLocationAddress')) {
                        this.locationAddress=this.$cookies.get('currentLocationAddress');
                        this.locationLatitude=this.$cookies.get('currentlocationLatitude');
                        this.locationLongitude=this.$cookies.get('currentlocationLongitude');
                    } else {
                        this.$parent.$parent.isLoading = true;
                        if (navigator.geolocation) {
                            var app = this;
                            navigator.geolocation.getCurrentPosition(position => {
                                var geocoder = new google.maps.Geocoder;
                                var latlng = {lat: position.coords.latitude, lng: position.coords.longitude};
                                geocoder.geocode({'location': latlng}, function (results, status) {
                                    if (status === 'OK') {
                                        if (results[0]) {
                                            app.shareLocation = true;
                                            app.$parent.$parent.isLoading = false;
                                            app.locationAddress = results[0].formatted_address;
                                            // app.locationAddress = results[0].address_components[2].long_name + ', ' + results[0].address_components[3].long_name + ', ' + results[0].address_components[4].long_name;
                                            app.locationLatitude = position.coords.latitude;
                                            app.locationLongitude = position.coords.longitude;
                                            this.$cookies.set('currentLocationAddress', app.locationAddress);
                                            this.$cookies.set('currentlocationLatitude', app.locationLatitude);
                                            this.$cookies.set('currentlocationLongitude', app.locationLongitude);
                                        } else {
                                            app.shareLocation = false;
                                            app.$parent.$parent.isLoading = false;
                                            app.locationAddress = '';
                                            app.locationLatitude = '';
                                            app.locationLongitude = '';
                                            app.$parent.$parent.showErrors({'message': ['Tarayıcınızın zedinga için lokasyon erişimi açık değil !']});
                                        }
                                    }
                                })
                            })
                        } else {
                            this.shareLocation = false;
                            this.$parent.$parent.isLoading = false;
                            this.locationAddress = '';
                            this.locationLatitude = '';
                            this.locationLongitude = '';
                            this.$parent.showErrors({'message': ['Tarayıcınızın zedinga için lokasyon erişimi açık değil !']});
                        }
                    }
                } else {
                    this.shareLocation = false;
                    this.$parent.$parent.isLoading = false;
                    this.locationAddress = '';
                    this.locationLatitude = '';
                    this.locationLongitude = '';
                }
            },
            getProfileInformation() {
                var _this = this;
                axios.get('/profile/get-general')
                    .then(response => {
                        _this.profile=response.data.profile;
                        if (_this.profile.privacies.location_privacy==1) {
                            _this.getLocation();
                        }
                    })
                    .catch(error=>{

                    });
            }
        }
    }
</script>

<style scoped>

</style>
