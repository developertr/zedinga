<template>
    <div class="ingaDetail">
        <a @click="$router.go(-1)" class="prev">
            geri
            {{ id }}
        </a>
        <inga class="inga" :index="inga.id" :key="inga.id" :inga="inga" v-if="inga">
        </inga>
    </div>
</template>

<script>
    export default {
        name: "IngaDetail",
        props : ['id'],
        data() {
            return {
                inga : []
            }
        },
        mounted() {
            this.getIngaDetails();
        },
        methods : {
            getIngaDetails() {
                self = this;
                axios.get('/inga/detail/'+this.id)
                    .then(response => {
                        self.inga = response.data;
                    })
                    .catch(error=>{

                    });
            },
            onSwipeRight() {
                this.$parent.$router.go(-1);
            }
        },
        watch : {
            id() {
                this.inga = [];
                this.getIngaDetails();
            }
        }
    }
</script>

<style scoped>

</style>
