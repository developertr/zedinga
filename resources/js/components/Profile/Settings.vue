<template>
    <div class="settings">
        <div class="settingsNav">
            <ul>
            <li @click="settingsFormType='general'" v-bind:class="{ 'active' : settingsFormType=='general' }">
                <img src="/svg/profile-account.svg" />
                Genel
            </li>
            <li @click="settingsFormType='locations'" v-bind:class="{ 'active' : settingsFormType=='locations' }">
                <img src="/svg/profile-locations.svg" />
                Konumlar
            </li>
            <li @click="settingsFormType='privacy'" v-bind:class="{ 'active' : settingsFormType=='privacy' }">
                <img src="/svg/profile-privacy.svg" />
                Gizlilik
            </li>
            <li>
                <img src="/svg/profile-statistics.svg" />
                İstatistikler
            </li>
            <li>
                <img src="/svg/profile-blocks.svg" />
                Engellenenler
            </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="settingsDetails">
            <div v-if="settingsFormType=='general'">
                <form @submit.prevent="generalSettingsSave()">
                    <h2>Genel</h2>
                    <div class="description">
                        <img src="/svg/information.svg" alt="">
                        Genel hesap bilgilerinizi bilgilerinizi düzenleyip , profilinizde görüntülenecek bilgileri değiştirebilirsiniz.
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label for="">Kullanıcı Adı</label>
                                <input type="text" class="form-control" name="" id="" aria-describedby="helpId" placeholder="" v-model="profile.username">
                                <small id="helpId" class="form-text text-muted">https://www.zedinga.com/{{ profile.username }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label for="">E-posta Adresi</label>
                                <input type="text" class="form-control" name="" id="" aria-describedby="helpId" v-model="profile.email">
                                <small id="helpId" class="form-text text-muted">E-posta adresin herkes tarafından görüntülenemeyecektir.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label for="">Ad Soyad</label>
                                <input type="text" class="form-control" name="" id="" aria-describedby="helpId" v-model="profile.name">
                                <small id="helpId" class="form-text text-muted">Profilinizde ve etkileşimlerinizde görüntülenemeyecektir.</small>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label for="">Cep Telefonu</label>
                                <input type="text" class="form-control" v-mask="'0##########'" v-model="profile.mobile_number">
                                <small id="helpId" class="form-text text-muted">Profilinizde ve etkileşimlerinizde görüntülenmeyecektir.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="">Kişisel Bilgiler</label>
                                <textarea class="form-control" name="" id="" rows="3" v-model="profile.description">{{ profile.description }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            Değişiklikleri Kaydet
                        </button>
                    </div>
                </form>
            </div>
            <div v-else-if="settingsFormType=='locations'">
                <div class="locations">
                    <h2>Konumlar</h2>
                    <div class="description">
                        <img src="/svg/information.svg" alt="">
                        <b>Zed</b> takibinde, konumlarınıza yakın olan <b>Zed</b>'ler ile bilgilendirilip etrafınızda olup bitenden haberdar olabilirsiniz. Ayrıca konumlarınıza yakın üyelerimizle yani komşularınızla takipleşme fırsatını da yakalamış olursunuz.
                    </div>
                    <div v-if="!newLocation">
                        <ul>
                            <li v-for="location in locations" :key="location.id">
                                <img src="/svg/maps-and-location.svg" alt="">
                                <h5 class="mb-1">{{ location.location_name }}</h5>
                                <p class="mb-1">{{ location.location_address }}</p>
                                <small>{{ location.location_latitude+','+location.location_longitude }}</small>
                                <button class="btn delete" @click="deleteLocation(location.id)">Konumu Sil</button>
                            </li>
                        </ul>
                        <div class="text-center">
                            <button class="btn" @click="newLocation=true">
                                + Konum Ekle
                            </button>
                        </div>
                    </div>
                    <form @submit.prevent="addNewLocation()" v-if="newLocation">
                        <div class="new">
                            <div class="form-group">
                                <label for="">Konum Adı</label>
                                <input type="text" class="form-control" name="" id="" aria-describedby="helpId" placeholder="" v-model="newLocationName">
                                <small id="helpId" class="form-text text-muted">Lütfen belirleyeceğiniz konuma bir isim belirtiniz. (Örn : Ev , İş)</small>
                            </div>
                            <button class="btn getMyLocation" type="button" @click="getLocation" v-bind:class="{ active: shareLocation }">
                                <img src="/svg/black-pin.svg" />
                                Mevcut Konumumu Kullan
                            </button>
                            <div v-if="!shareLocation">
                                <div class="or">veya</div>
                                <div class="form-group">
                                    <label for="">Adresi Yazınız</label>
                                    <gmap-autocomplete
                                        @place_changed="setPlace" class="form-control" v-model="currentAddress" :component-restrictions="{country: 'tr'}" :locality="['TR']">
                                    </gmap-autocomplete>
                                    <small id="helpId" class="form-text text-muted">Konumun koordinasyon bilgilerini alabilmemiz için lütfen adresinizi yazınız. Bu adres paylaşımlarınızda görüntülenmeyecektir</small>
                                </div>
                            </div>
                            <div class="addressPreview" v-if="newLocationAddress">
                                <b>Eklenecek Adres</b>
                                {{ newLocationAddress }}
                                <br/><i>{{ newLocationLatitude+','+newLocationLongitude }}</i>
                            </div>
                            <button type="submit" class="btn btn-primary" v-if="newLocationAddress">Lokasyonu Ekle</button>
                        </div>
                    </form>
                </div>
            </div>
            <div v-else-if="settingsFormType=='privacy'">
                <form @submit.prevent="privacySettingsSave()">
                <h2>Gizlilik</h2>
                <div class="description">
                    <img src="/svg/information.svg" alt="">
                    Profiliniz ile ilgili gizlilik ve güvenlik ayarlarını bu alandan gerçekleştirebilirsiniz.
                </div>
                <div class="form-group" v-if="profile.privacies">
                    <b>Takip gizliliği</b><br/>
                    Profilini korumaya al<br/>
                    <select class="custom-select" v-model="profile.privacies.follow_privacy">
                        <option value="1">Beni herkes takip edebilir.</option>
                        <option value="2">Beni sadece takip ettiklerim takip edebilir.</option>
                        <option value="3">Takip teklifine onay verdiklerim beni takip edebilir.</option>
                    </select>
                </div>
                <hr>
                <div class="form-check" v-if="profile.privacies">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="" id="" value="checkedValue" v-model="profile.privacies.post_privacy">
                        <b>Inga Paylaşım Gizliliği</b><br/>
                        <b>Inga</b> Paylaşımlarımı sadece takipçilerim görebilir.<br/>
                        <small>Bu seçildiğinde <b>Inga</b> paylaşımlarını sadece takipçilerin görebilir ve etkileşimde bulunabilir.</small>
                    </label>
                </div>
                <hr>
                <div class="form-check" v-if="profile.privacies">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="" id="" value="checkedValue" v-model="profile.privacies.location_privacy">
                        <b>Konum paylaşımı</b><br/>
                        Paylaşımlarına konum ekleyebilirsin.<br/>
                        <small>Bu seçildiğinde paylaşımlarına sen iptal etmediğin sürece paylaşımı yaptığın andaki konum bilgilerin de eklenecektir.</small>
                    </label>
                </div>
                <hr>
                <div class="form-check" v-if="profile.privacies">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="" id="" value="checkedValue" v-model="profile.privacies.message_privacy">
                        <b>Mesaj gizliliği</b><br/>
                        Herhangi bir üyeden mesaj alabilirsin.<br/>
                        <small>Bu seçildiğinde, takip etmesen bile herhangi bir Zedinga kullanıcısından mesaj alabileceksin.</small>
                    </label>
                </div>
                <hr>
                <div class="form-check" v-if="profile.privacies">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="" id="" value="checkedValue" v-model="profile.privacies.email_search_privacy">
                        <b>E-posta ile bulunma</b><br/>
                        <small>Zedinga'da kayıtlı e-posta adresini aratarak seni bulmalarına izin ver.</small>
                    </label>
                </div>
                <hr>
                <div class="form-check" v-if="profile.privacies">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="" id="" value="checkedValue" v-model="profile.privacies.mobile_search_privacy">
                        <b>Telefon numarası ile bulunma</b><br/>
                        <small>Zedinga'da kayıtlı telefon numaranı aratarak seni bulmalarına izin ver.</small>
                    </label>
                </div>
                <hr>
                <div>
                    <button type="submit" class="btn btn-primary">Gizlilik Değişikliklerini Kaydet</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import Vue from 'vue';
    import VueMask from 'v-mask';
    Vue.use(VueMask);

    export default {
        name: "Settings",
        data() {
            return {
                settingsFormType : 'general',
                profile : [],
                locations : [],
                newLocation : false,
                shareLocation : false,
                trueAddress : false,
                currentAddress : '',
                newLocationName : '',
                newLocationLatitude : '',
                newLocationLongitude : '',
                newLocationAddress : ''
            }
        },
        mounted() {
            this.getProfileInformation();
            this.getProfileLocations();
        },
        methods: {
            deleteLocation(id) {
                var app = this;
                axios.post('/profile/location-delete',{
                    id : id
                }).then(response=> {
                    if (response.status==200) {
                        if (response.data.success) {
                            app.locations = response.data.locations;
                        }
                    } else {
                        this.$parent.showErrors(response.data.message);
                    }
                }).catch(error=>{
                    alert(error);
                });
            },
            addNewLocation() {
                this.$parent.isLoading = true;
                var app = this;
                axios.post('/profile/location-add',
                    {
                        share_location : app.shareLocation,
                        true_address : app.trueAddress,
                        location_name : app.newLocationName,
                        location_latitude : app.newLocationLatitude,
                        location_longitude : app.newLocationLongitude,
                        location_address : app.newLocationAddress
                    }
                ).then(response=>{
                    if(response.status==200) {
                        this.$parent.isLoading = false;
                        if(response.data.success) {
                            this.newLocation = false;
                            this.trueAddress = false;
                            this.shareLocation = false;
                            this.newLocationName = '';
                            this.newLocationLatitude = '';
                            this.newLocationLongitude = '';
                            this.newLocationAddress = '';
                            this.getProfileLocations();
                            this.$snotify.success('Konumunuz Başarıyla Kaydedildi');
                        } else {
                            this.$parent.showErrors(response.data.message);
                        }
                    }
                }).catch(error=>{
                    this.$parent.isLoading = false;
                    this.$parent.showErrors({'message':['Beklenmedik bir hata oluştu. Lütfen daha sonra tekrar deneyiniz']});
                })
            },
            getLocation() {
                this.shareLocation = !this.shareLocation;
                if (this.shareLocation) {
                    this.trueAddress = false;
                    this.newLocationAddress = '';
                    this.newLocationLatitude = '';
                    this.newLocationLongitude = '';
                    if (navigator.geolocation) {
                        var app = this;
                        navigator.geolocation.getCurrentPosition(position => {
                            var geocoder = new google.maps.Geocoder;
                            var latlng = {lat: position.coords.latitude, lng: position.coords.longitude};
                            geocoder.geocode({'location': latlng}, function (results, status) {
                                if (status === 'OK') {
                                    if (results[0]) {
                                        app.shareLocation = true;
                                        app.trueAddress = true;
                                        app.newLocationAddress = results[0].formatted_address;
                                        app.newLocationLatitude = position.coords.latitude;
                                        app.newLocationLongitude = position.coords.longitude;
                                    } else {
                                        app.shareLocation = false;
                                        app.trueAddress = false;
                                        app.newLocationAddress = '';
                                        app.newLocationLatitude = '';
                                        app.newLocationLongitude = '';
                                        this.$parent.showErrors({'message': ['Tarayıcınızın zedinga için lokasyon erişimi açık değil !']});
                                    }
                                }
                            })
                        })
                    } else {
                        this.shareLocation = false;
                        this.trueAddress = false;
                        this.newLocationAddress = '';
                        this.newLocationLatitude = '';
                        this.newLocationLongitude = '';
                        this.$parent.showErrors({'message': ['Tarayıcınızın zedinga için lokasyon erişimi açık değil !']});
                    }
                } else {
                    this.shareLocation = false;
                    this.trueAddress = false;
                    this.newLocationAddress = '';
                    this.newLocationLatitude = '';
                    this.newLocationLongitude = '';
                }

            },
            setPlace(place) {
                if (place.geometry) {
                    this.shareLocation = false;
                    this.trueAddress = true;
                    this.newLocationLatitude = place.geometry.location.lat();
                    this.newLocationLongitude = place.geometry.location.lng();
                    this.newLocationAddress = place.formatted_address;
                } else {
                    this.trueAddress = false;
                    this.shareLocation = false;
                    this.newLocationLatitude = '';
                    this.newLocationLongitude = '';
                    this.newLocationAddress = '';
                    this.$parent.showErrors({'message':['Lütfen geçerli bir adres giriniz']});
                }
            },
            getProfileInformation() {
                var _this = this;
                axios.get('/profile/get-general')
                .then(response => {
                    _this.profile=response.data.profile;
                })
                .catch(error=>{

                });
            },
            getProfileLocations() {
                var _this = this;
                axios.get('/profile/get-locations')
                    .then(response => {
                        _this.locations=response.data.locations;
                    })
                    .catch(error=>{

                    });
            },
            generalSettingsSave() {
                this.$parent.isLoading = true;
                var app = this;
                axios.post('/profile/general-save',
                    {
                        username : app.profile.username,
                        email : app.profile.email,
                        name : app.profile.name,
                        mobile_number : app.profile.mobile_number,
                        personal_information : app.profile.description
                    }
                ).then(response=>{
                    if(response.status==200) {
                        this.$parent.isLoading = false;
                        if(response.data.success) {
                            this.$snotify.success('Değişiklikler Kaydedildi');
                        } else {
                            this.$parent.showErrors(response.data.message);
                        }
                    }
                }).catch(error=>{
                    this.$parent.isLoading = false;
                    this.$parent.showErrors({'message':['Beklenmedik bir hata oluştu. Lütfen daha sonra tekrar deneyiniz']});
                })
            },
            privacySettingsSave() {
                this.$parent.isLoading = true;
                var app = this;
                axios.post('/profile/privacy-save',
                    {
                        follow_privacy : app.profile.privacies.follow_privacy,
                        post_privacy : app.profile.privacies.post_privacy,
                        location_privacy : app.profile.privacies.location_privacy,
                        message_privacy : app.profile.privacies.message_privacy,
                        email_search_privacy : app.profile.privacies.email_search_privacy,
                        mobile_search_privacy : app.profile.privacies.mobile_search_privacy
                    }
                ).then(response=>{
                    if(response.status==200) {
                        this.$parent.isLoading = false;
                        if(response.data.success) {
                            this.$snotify.success('Değişiklikler Kaydedildi');
                        } else {
                            this.$parent.showErrors(response.data.message);
                        }
                    }
                }).catch(error=>{
                    this.$parent.isLoading = false;
                    this.$parent.showErrors({'message':['Beklenmedik bir hata oluştu. Lütfen daha sonra tekrar deneyiniz']});
                })
            }
        }
    }
</script>

<style scoped>

</style>
