<?php

namespace App\Http\Controllers;

use App\Post;
use App\PostLikesDislikes;
use App\UserCounter;
use App\Follower;
use App\User;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IngaController extends Controller
{
    public function ingaDetail($id) {
        if (is_numeric($id)) {
            $post = Post::withUser()->where('posts.id',$id)->first();
            if (isset($post)) {
                $post->my_actions = PostLikesDislikes::where('user_id',Auth::user()->id)->where('post_id',$post->id)->whereIn('like_type',[0,1,2])->first();
                $post->my_score = PostLikesDislikes::where('user_id',Auth::user()->id)->where('post_id',$post->id)->where('like_type',3)->first();
            }
            return Response::json($post);
        }
    }

    public function commentEnableDisable(Request $request) {
        $post = Post::where('id',$request->id)->where('user_id',Auth::user()->id)->first();
        if(isset($post)) {
            if ($post->comment_is_on==1) { $post->comment_is_on = 0; }
            else { $post->comment_is_on = 1; }
            $post->save();
            return Response::json(['nowCommentStatus'=>$post->comment_is_on]);
        }
    }

    public function rateIt(Request $request) {
        if(($request->rate>=0) && ($request->rate<=5)) {
            $post = Post::where('id',$request->id)->first();
            if(isset($post)) {
                if ($post->user_id==Auth::user()->id) {
                    return Response::json(['success'=>false,'error'=>'Kendi yazına skor veremezsin']);
                } else {
                    if (User::where('id', $post->user_id)->first()->privacies()->first()->post_privacy == 1) {
                        if (!Follower::where('follower_id', Auth::user()->id)->where('user_id', $post->user_id)->where('approval', 1)->first()) {
                            return Response::json(['success' => false, 'error' => 'Bu kullanıcının inga paylaşımlarıyla sadece takipçileri etkileşime geçebilir.']);
                        }
                    }
                }

                $userCounter = UserCounter::where('user_id', $post->user_id)->first();
                $beforeAction = PostLikesDislikes::where('user_id', Auth::user()->id)->where('post_id', $post->id)->where('like_type',3)->first();
                if (isset($beforeAction)) {
                    $beforeAction->score_value = $request->rate;
                    $beforeAction->save();
                } else {
                    $beforeAction = new PostLikesDislikes();
                    $beforeAction->user_id = Auth::user()->id;
                    $beforeAction->post_id = $post->id;
                    $beforeAction->like_type = 3;
                    $beforeAction->score_value = $request->rate;
                    $beforeAction->save();
                    $post->score_count = $post->score_count + 1;

                    $userCounter->post_vote = $userCounter->post_vote + 1;
                    $userCounter->save();
                }
                $post->score_average = PostLikesDislikes::where('post_id', $post->id)->where('like_type',3)->avg('score_value');
                $post->save();
                return Response::json(['rate'=>$post->score_average,'score_count'=>$post->score_count]);
            }
        }
    }

    public function like(Request $request) {
        $like_types = [1,2];
        if (in_array($request->like_type,$like_types)) {
            $post = Post::where('id',$request->id)->first();
            if(isset($post)) {
                $userCounter = UserCounter::where('user_id',$post->user_id)->first();
                if ($post->user_id!=Auth::user()->id) {
                    if (User::where('id', $post->user_id)->first()->privacies()->first()->post_privacy == 1) {
                        if (!Follower::where('follower_id', Auth::user()->id)->where('user_id', $post->user_id)->where('approval', 1)->first()) {
                            return Response::json(['success' => false, 'error' => 'Bu kullanıcının inga paylaşımlarıyla sadece takipçileri etkileşime geçebilir.']);
                        }
                    }
                }
                $beforeAction = PostLikesDislikes::where('user_id',Auth::user()->id)->where('post_id',$post->id)->whereIn('like_type',[0,1,2])->first();
                if (isset($beforeAction)) {
                    if ($beforeAction->like_type==$request->like_type) {
                        $beforeAction->like_type = 0;
                        $beforeAction->save();
                        if($request->like_type==1) {
                            $post->like_count = $post->like_count - 1;
                            $userCounter->post_like = $userCounter->post_like - 1;
                        } elseif($request->like_type==2) {
                            $post->dislike_count = $post->dislike_count - 1;
                            $userCounter->post_dislike = $userCounter->post_dislike - 1;
                        }
                    } else {
                        if($request->like_type==1) {
                            $post->like_count = $post->like_count + 1;
                            $userCounter->post_like = $userCounter->post_like + 1;
                            if ($beforeAction->like_type<>0) {
                                $post->dislike_count = $post->dislike_count - 1;
                                $userCounter->post_dislike = $userCounter->post_dislike - 1;
                            }
                        } elseif($request->like_type==2) {
                            $post->dislike_count = $post->dislike_count + 1;
                            $userCounter->post_dislike = $userCounter->post_dislike + 1;
                            if ($beforeAction->like_type<>0) {
                                $post->like_count = $post->like_count - 1;
                                $userCounter->post_like = $userCounter->post_like - 1;
                            }
                        }
                        $beforeAction->like_type = $request->like_type;
                        $beforeAction->save();
                    }
                } else {
                    $beforeAction = new PostLikesDislikes();
                    $beforeAction->user_id = Auth::user()->id;
                    $beforeAction->post_id = $post->id;
                    $beforeAction->like_type = $request->like_type;
                    $beforeAction->save();
                    if($request->like_type==1) {
                        $post->like_count = $post->like_count + 1;
                        $userCounter->post_like = $userCounter->post_like + 1;
                    } elseif($request->like_type==2) {
                        $post->dislike_count = $post->dislike_count + 1;
                        $userCounter->post_dislike = $userCounter->post_dislike + 1;
                    }

                    if($post->user_id<>Auth::user()->id) {
                        $totalActionCount = $post->like_count + $post->dislike_count + $post->comment_count;
                        if(($totalActionCount % env('POST_ORDER_UPDATE_ACTION'))==0) {
                            $post->post_order_date = date('Y-m-d H:i:s');
                        }
                    }
                }
                $post->save();
                $userCounter->save();
                return Response::json(['success'=>true,'like_count'=>$post->like_count,'dislike_count'=>$post->dislike_count,'myAction'=>$beforeAction]);
            }
        }
    }

    public function getMyActionThisInga($id) {
        if (is_numeric($id)) {
            $myAction = PostLikesDislikes::where('user_id',Auth::user()->id)->where('post_id',$id)->first();
            return Response::json(['myAction'=>$myAction]);
        }
    }
}
