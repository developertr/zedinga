<template>
    <div>
        <div class="score">
            {{member.score}}
            <div class="count">
                <b>{{ counters.post_vote }}</b>
                <label for="">kez oylandın</label>
            </div>
        </div>
        <div class="avatar">
            <img v-bind:src="avatar" alt="">
        </div>
        <div class="name">
            {{ member.name }}
            <div class="username">
                {{ member.username }}
            </div>
        </div>
        <div class="counters">
            <div class="count"><b>{{ this.$parent.formatNumber(counters.friend) }}</b> takip</div>
            <div class="count"><b>{{ this.$parent.formatNumber(counters.follower) }}</b> takipçi</div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Info",
        props : {
            user : {
                type : Object,
                default : []
            },
            counters: {
                type : Object,
                default : []
            }
        },
        data () {
            return {
                avatar : '',
                member : this.user
            }
        },
        mounted () {
            if (this.member.profile_image_upload==1) {
                this.avatar = '/uploads/avatars/'+this.member.profile_image;
            } else {
                if (this.member.profile_image==null) {
                    this.avatar = '/images/logo.png';
                } else {
                    this.avatar = this.member.profile_image;
                }
            }
        }
    }
</script>

<style scoped>

</style>
