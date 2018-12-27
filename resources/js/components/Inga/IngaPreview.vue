<template>
    <div>
        <div class="avatar" @click="profilePreviewActivate">
            <img v-if="inga.profile_image_upload" v-bind:src="'/uploads/avatars/thumbs/'+inga.profile_image" />
            <img v-else-if="inga.profile_image" v-bind:src="inga.profile_image">
            <img v-else src="/images/logo.png" />
        </div>
        <profile-preview-box class="profilePreview" :userId="profilePreviewId" v-show="profilePreview" v-if="inga.user_id"></profile-preview-box>
        <!--<div class="content" v-html=""></div>-->
        <div class="ingaContent">
            <div class="username">
                <b>{{inga.name}}</b>
                <span>@{{inga.username}}</span>
                <label><timeago :datetime="inga.created_at" :auto-update="60" locale="tr"></timeago></label>
            </div>
            <!--v-if="inga.length>0"-->
            <router-link :to="{ name: 'ingaDetail', params: { id: inga.id }}" v-if="inga.id">
                <div class="ingaText">
                    <nl2br tag="div" :text="inga.content"></nl2br>
                </div>
            </router-link>
        </div>
        <div class="postImage" v-if="inga.post_content_type==1" v-bind:class="{ preview : !imageViewFull }" @click="imageView">
            <img v-bind:src="'/uploads/posts/thumbs/'+inga.image" />
        </div>
        <div class="postVideo embed-responsive embed-responsive-16by9" v-if="inga.post_content_type==2" @click="videoThumbnailClick" v-show="!showVideo">
            <img v-bind:src="'/uploads/posts/thumbs/'+inga.image" class="embed-responsive-item" />
        </div>
        <div class="postVideo embed-responsive embed-responsive-16by9" v-if="inga.post_content_type==2" v-show="showVideo">
            <youtube class="embed-responsive-item" v-if="inga.video_website=='youtube'" :video-id="inga.video_id" :player-vars="{ controls:0 , showinfo:0 , rel:0 , autoplay:0 }"  ref="youtube"></youtube>
            <vimeo-player ref="player" v-else-if="inga.video_website=='vimeo'" :video-id="inga.video_id" :auto-play="false" :options="{}" class="embed-responsive-item"/>
            <iframe v-else-if="inga.video_website=='dailymotion'" frameborder="0" width="480" height="270" v-bind:src="'//www.dailymotion.com/embed/video/'+inga.video_id+'?autoplay=1&ui-logo=false&controls=0'" allowfullscreen allow="autoplay" class="embed-responsive-item"></iframe>
        </div>
        <div v-if="inga.share_location" class="location">
            <img src="/svg/black-pin.svg" alt="">
            {{ inga.address }}
        </div>
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="rating">
                    <image-rating src="/images/star.png" @rating-selected="rateIt($event)" :increment="0.1" :rating="rating" :item-size="30"></image-rating>
                    <div class="score">
                        <b>{{ score_count }}</b> kez oylandı
                    </div>
                    <div class="score" v-if="my_score>=0">
                        , sen <b>{{ my_score }}</b> skor kullandın
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="counts">
                    <div @click="likeClick(1)" class="likeCount">
                        <img src="/svg/like-active.svg" v-if="likeThis">
                        <img src="/svg/like.svg" alt="" v-else >
                        {{like_count}}
                    </div>
                    <div @click="likeClick(2)" class="disLikeCount">
                        <img src="/svg/dislike-active.svg" v-if="disLikeThis">
                        <img src="/svg/dislike.svg" alt="" v-else >
                        {{dislike_count}}
                    </div>
                    <div v-show="comment_is_on">
                        <img src="/svg/comments.svg" alt="">
                        {{inga.comment_count}}
                    </div>
                </div>
            </div>
        </div>

        <div class="ingaSettingsButton" v-show="myUserId==inga.user_id">
            <!--<VueDropdown>-->
                <!--<VueButton-->
                    <!--slot="trigger"-->
                    <!--icon-left="more_vert"-->
                    <!--class="icon-button flat"-->
                <!--/>-->
                <!--<VueSwitch icon="comment" v-model="comment_is_on" class="extend-left">Yoruma Açık</VueSwitch>-->
                <!--<VueDropdownButton @click="deleteThis">Sil</VueDropdownButton>-->
            <!--</VueDropdown>-->
        </div>
        <div class="ingaComments">

        </div>

    </div>
</template>

<script>
    export default {
        name: "IngaPreview",
        props: ['inga','index','myUserId'],
        data() {
            return {
                rating : Number(this.inga.score_average),
                nowRating : Number(this.inga.score_average),
                like_count : this.inga.like_count,
                score_count : this.inga.score_count,
                dislike_count : this.inga.dislike_count,
                my_score : -1,
                likeThis : false,
                disLikeThis : false,
                id:this.inga.id,
                imageViewFull : false,
                nowCommentStatus : this.inga.comment_is_on,
                comment_is_on : this.inga.comment_is_on,
                showVideo : false,
                profilePreview : false,
                profilePreviewId : 0
            }
        },
        mounted() {
            if (this.inga.my_actions) {
                this.activateLikeButton(this.inga.my_actions.like_type);
            }
            if (this.inga.my_score) {
                this.my_score = this.inga.my_score.score_value;
            }
        },
        methods: {
            videoThumbnailClick() {
              this.showVideo = true;
              if(this.inga.video_website=='vimeo') {
                  this.$refs.player.play();
              } else if (this.inga.video_website=='youtube') {
                  this.$refs.youtube.player.playVideo()
              }
            },
            rateIt(event) {
                var _this = this;
                axios.post('/inga/rate',{
                    id : _this.inga.id,
                    rate : event
                }).then(response=> {
                    if (response.status==200) {
                        if (response.data.rate) {
                            _this.rating = Number(response.data.rate);
                            _this.score_count = response.data.score_count;
                            _this.my_score = event;
                        } else {
                            this.$snotify.error(response.data.error);
                            _this.rating = Number(_this.inga.score_average);
                        }
                    }
                }).catch(error=>{
                });
            },
            deleteThis() {
                var _this = this;
                axios.post('/inga/delete',{
                    id : _this.inga.id
                }).then(response=> {
                    if (response.status==200) {
                        if (response.data.success) {
                            _this.$parent.ingas.splice(_this.index,1);
                        }
                    }
                }).catch(error=>{
                    alert(error);
                });
            },
            commentDisableEnable() {
                var _this = this;
                axios.post('/inga/comment-is-on-toggle',{
                    id : _this.inga.id
                }).then(response=> {
                    if (response.status==200) {
                        _this.nowCommentStatus = response.data.nowCommentStatus;
                    }
                }).catch(error=>{
                    alert(error);
                });
            },
            likeClick(likeType) {
                var app = this;
                axios.post('/inga/like',{
                    id : app.inga.id,
                    like_type : likeType
                }).then(response=> {
                    if (response.status == 200) {
                        if (response.data.success) {
                            this.like_count = response.data.like_count;
                            this.dislike_count = response.data.dislike_count;
                            if (response.data.myAction) {
                                app.activateLikeButton(response.data.myAction.like_type);
                            }
                        } else {
                            this.$snotify.error(response.data.error);
                        }
                    }
                }).catch(error=>{
                    alert(error);
                });
            },
            activateLikeButton(likeType) {
                if (likeType==1) {
                    this.likeThis = true;
                    this.disLikeThis = false;
                } else if (likeType==2) {
                    this.likeThis = false;
                    this.disLikeThis = true;
                } else {
                    this.likeThis = false;
                    this.disLikeThis = false;
                }
            },
            imageView() {
                this.imageViewFull = !this.imageViewFull;
            },
            profilePreviewActivate() {
                this.profilePreviewId = this.inga.user_id;
                this.profilePreview = !this.profilePreview
            }
        },
        watch: {
            comment_is_on() {
                this.commentDisableEnable();
            }
        }
    }
</script>

<style scoped>

</style>
