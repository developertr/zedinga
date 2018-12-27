<template>
    <div class="profileIngasLists">
        <div class="newIngaCount" v-if="newIngaCount > 0">
            <span @click="getIngas()">{{ newIngaCount }} yeni inga...</span>
        </div>
        <inga v-for="(inga , index) in ingas" class="inga" :index="index" :key="inga.id" :inga="inga">
        </inga>

        <div @click="moreIngas()" v-if="moreIngaShow" class="moreIngas">
            <span>
            Daha fazla
            </span>
        </div>
    </div>
</template>

<script>
    export default {
        name: "List",
        data() {
            return {
                ingas : [],
                newIngas : [],
                lastIngaId : 0,
                newIngaCount : 0,
                perPage : 1,
                moreIngaShow : true
            }
        },
        mounted() {
            this.getIngas();
            var self = this;
            setInterval( function(){
                self.getNewIngaCount()
            },15000);
        },
        methods: {
            moreIngas() {
                this.perPage ++ ;
                this.getIngas(1);
            },
            getNewIngaCount() {
                if (this.newIngaCount<100) {
                    axios.get('/profile/my-profile-new-inga-count/'+this.lastIngaId)
                        .then(response => {
                            this.newIngaCount = response.data;
                        })
                        .catch(error=>{

                        });
                }
            },
            getIngas(addPush=0) {
                axios.get('/profile/my-profile-ingas?page='+this.perPage)
                    .then(response => {
                        this.newIngaCount = 0;
                        if(addPush==0) {
                            if(response.data.data.length<50) {
                                this.moreIngaShow = false;
                            }
                            this.ingas = response.data.data;
                            this.lastIngaId = response.data.data[0].id;
                        } else {
                            if (response.data.data.length==0) {
                                this.moreIngaShow = false;
                            } else {
                                for (var x = 0; x < response.data.data.length; x++) {
                                    var inga = response.data.data[x];
                                    this.ingas.push(inga);
                                }
                            }
                        }
                        // this.ingas.push(response.data.data);
                    })
                    .catch(error=>{

                    });
            },
            getLastMyInga() {
                var app = this;
                axios.get('/profile/my-profile-last-inga/')
                    .then(response => {
                        app.ingas.unshift(response.data[0]);
                        // this.lastIngaId = response.data[0].id;
                    })
                    .catch(error=>{

                    });
            }
        }
    }
</script>

<style scoped>

</style>
