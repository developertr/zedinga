<template>
    <div class="profilePreview shadow">
        <div class="profilePicture" v-bind:style="{ backgroundImage: 'url(' + cover + ')' }">
            <div class="score">
                {{ member.score }}
            </div>
            <div class="image">
                <img v-if="member.profile_image_upload" v-bind:src="'/uploads/avatars/thumbs/'+member.profile_image" />
                <img v-else-if="member.profile_image" v-bind:src="member.profile_image">
                <img v-else src="/images/logo.png" />
            </div>
        </div>
        <div class="informations">
            <div class="name">{{ member.name }}</div>
            <div class="username">{{ member.username }}</div>
        </div>
        <div class="clearfix"></div>
    </div>
</template>

<script>
    export default {
        name: "ProfilePreviewBox",
        props: ['userId'],
        data() {
            return {
                member : [],
                cover : ''
            }
        },
        watch: {
            userId() {
                if (this.userId>0) {
                    self = this;
                    axios.get('/inga/user-information/'+this.userId)
                        .then(response => {
                            self.member = response.data;
                            if (self.member.profile_image_upload==1) {
                                self.cover = '/uploads/covers/'+this.member.cover_image;
                            } else {
                                if (self.member.profile_image==null) {
                                    self.cover = '/images/cover.jpg';
                                } else {
                                    self.cover = self.member.cover_image;
                                }
                            }
                        })
                        .catch(error=>{

                        });
                }
            }
        }
    }
</script>

<style scoped>

</style>
