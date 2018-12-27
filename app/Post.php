<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public function user() {
        return $this->belongsTo('App\User','user_id','id');
    }

    public function scopeWithUser($sql) {
        $id = \Auth::user()->id;
        $sql->whereIn('user_id', function($query) use($id)
        {
            $query->select('user_id')
                ->from('followers')
                ->where('follower_id', $id)->where('approval',1);
        })
            ->orWhere('user_id', $id)
            ->leftjoin('users','users.id', '=', 'posts.user_id')
            ->addSelect('users.username')
            ->addSelect('users.name')
            ->addSelect('users.profile_image_upload')
            ->addSelect('users.profile_image')
            ->addSelect('posts.id')
            ->addSelect('posts.user_id')
            ->addSelect('posts.content')
            ->addSelect('posts.image')
            ->addSelect('posts.like_count')
            ->addSelect('posts.dislike_count')
            ->addSelect('posts.comment_count')
            ->addSelect('posts.share_count')
            ->addSelect('posts.score_count')
            ->addSelect('posts.score_average')
            ->addSelect('posts.share_location')
            ->addSelect('posts.address')
            ->addSelect('posts.latitude')
            ->addSelect('posts.longitude')
            ->addSelect('posts.post_content_type')
            ->addSelect('posts.video_website')
            ->addSelect('posts.video_id')
            ->addSelect('posts.comment_is_on')
            ->addSelect('posts.created_at');
    }
}
