<?php

namespace App\Http\Controllers;

use App\UserLocations;
use Illuminate\Http\Request;
use Auth;
use Response;
use Illuminate\Support\Facades\Validator;
use App\User;
use Image;
Use App\Post;
use App\PostLikesDislikes;

class ProfileController extends Controller
{
    public function index() {
        $overall_score = number_format(Post::where('user_id',Auth::user()->id)->where('score_count','>',0)->avg('score_average'),1);
        if (Auth::user()->score<>$overall_score) {
            Auth::user()->score = $overall_score;
            Auth::user()->save();
        }
        return view('profile.dashboard');
    }

    public function getIngas() {
        $posts = Post::withUser()->orderByDesc('post_order_date')->paginate(50);
        foreach($posts as $post) {
            $post->my_actions = PostLikesDislikes::where('user_id',Auth::user()->id)->where('post_id',$post->id)->whereIn('like_type',[0,1,2])->first();
            $post->my_score = PostLikesDislikes::where('user_id',Auth::user()->id)->where('post_id',$post->id)->where('like_type',3)->first();
        }
        return Response::json($posts);
    }

    public function getNewIngaCount($lastId) {
        $count = Post::withUser()->where('posts.id','>',$lastId)->where('posts.user_id','<>',Auth::user()->id)->count();
        return Response::json($count);
    }

    public function myLastInga() {
        $posts = Post::withUser()->where('posts.user_id',Auth::user()->id)->orderByDesc('posts.id')->take(1)->get();
        foreach($posts as $post) {
            $post->my_actions = PostLikesDislikes::where('user_id',Auth::user()->id)->where('post_id',$post->id)->whereIn('like_type',[0,1,2])->first();
            $post->my_score = PostLikesDislikes::where('user_id',Auth::user()->id)->where('post_id',$post->id)->where('like_type',3)->first();
        }
        return Response::json($posts);
    }

    public function newIngaSave(Request $request) {
        $validator = Validator::make($request->all(), [
            'message' => 'required|max:500'
        ]);
        if ($validator->fails()) {
            return Response::json(['success' => false, 'errors' => $validator->errors()]);
        } else {
            if ($request->sharelocation) {
                $validator = Validator::make($request->all(), [
                    'address' => 'required|max:100',
                    'latitude' => 'required|max:30',
                    'longitude' => 'required|max:30'
                ]);
                if ($validator->fails()) {
                    return Response::json(['success' => false, 'errors' => $validator->errors()]);
                }
            }

            $post_content_type = 0;
            $image = $request->image;

            if ($request->image_upload) {
                $validator = Validator::make($request->all(), [
                    'image' => 'required',
                ]);
                if ($validator->fails()) {
                    return Response::json(['success' => false, 'errors' => $validator->errors()]);
                }
                $post_content_type=1;
            }

            if ($request->video_select) {
                $validator = Validator::make($request->all(), [
                    'video_url' => 'required',
                    'video_website' => 'required',
                    'video_thumbnail' => 'required',
                    'video_id' => 'required',
                    'video_detect' => 'accepted',
                ]);
                if ($validator->fails()) {
                    return Response::json(['success' => false, 'errors' => $validator->errors()]);
                }
                $post_content_type=2;

                $image = Auth::user()->id.'-'.$request->video_website.'-'.$request->video_id.'.jpg';
                Image::make($request->video_thumbnail)->save(public_path('/uploads/posts/' . $image));
                Image::make($request->video_thumbnail)->save(public_path('/uploads/posts/thumbs/' . $image));
            }


            $post = new Post();
            $post->user_id = Auth::user()->id;
            $post->reference_user_id = Auth::user()->id;
            $post->post_content_type = $post_content_type;
            $post->content = $request->message;
            $post->hashtags = $this->getHashTags($request->message);
            $post->image = str_replace('/uploads/posts/thumbs/','',$image);
            $post->post_order_date = date('Y-m-d H:i:s');
            $post->share_location = $request->sharelocation;
            $post->address = $request->address;
            $post->latitude = $request->latitude;
            $post->longitude = $request->longitude;
            $post->video_website = $request->video_website;
            $post->video_id = $request->video_id;
            $post->save();

            $counters = Auth::user()->counters()->first();
            $counters->post = $counters->post + 1;
            $counters->save();
            return Response::json(['success'=>true,'post'=>$post]);
        }
    }

    public function getHashTags($text)
    {
        //Match the hashtags
        preg_match_all('/(^|[^a-z0-9_])#([a-z0-9_]+)/i', $text, $matchedHashtags);
        $hashtag = '';
        // For each hashtag, strip all characters but alpha numeric
        if(!empty($matchedHashtags[0])) {
            foreach($matchedHashtags[0] as $match) {
                $hashtag .= preg_replace("/[^a-z0-9]+/i", "", $match).',';
            }
        }
        //to remove last comma in a string
        return rtrim($hashtag, ',');
    }

    public function ingaVideoDetect(Request $request) {
        $url = $request->get('url');
        return Response::json($this->detectVideo($url));
    }

    function detectVideo($url) {
        $data['id']='';
        $data['thumbnail']='';
        $data['website']='';
        if (strstr($url, 'youtube')) {
            $data['id']=$this->extractVideoID($url);
            $data['thumbnail']=$this->getYouTubeThumbnailImage($data['id']);
            $data['website']='youtube';
        } elseif (strstr($url, 'vimeo')) {
            $data['id']=$this->getVimeoId($url);
            $data['thumbnail']=$this->getVimeoThumb($data['id']);
            $data['website']='vimeo';
        } elseif (strstr($url, 'dailymotion')) {
            $data['id']=$this->getDailyMotionId($url);
            $data['thumbnail']=$this->getDailymotionThumb($data['id']);
            $data['website']='dailymotion';
        }

//        $filename = Auth::user()->id.'-'.$data['website'].'-'.$data['id'].'.jpg';
//        Image::make($data['thumbnail'])->save(public_path('/uploads/posts/' . $filename));
//        Image::make($data['thumbnail'])->save(public_path('/uploads/posts/thumbs/' . $filename));

        return $data;
    }

    function getDailyMotionId($url)
    {
        if (preg_match('!^.+dailymotion\.com/(video|hub)/([^_]+)[^#]*(#video=([^_&]+))?|(dai\.ly/([^_]+))!', $url, $m)) {
            if (isset($m[6])) {
                return $m[6];
            }
            if (isset($m[4])) {
                return $m[4];
            }
            return $m[2];
        }
        return false;
    }

    function getDailymotionThumb($id) {
        $thumbnail_large_url='https://api.dailymotion.com/video/'.$id.'?fields=thumbnail_large_url'; //pass thumbnail_large_url, thumbnail_medium_url, thumbnail_small_url for different sizes
        $json_thumbnail = file_get_contents($thumbnail_large_url);
        $arr_dailymotion = json_decode($json_thumbnail, TRUE);
        $thumb=$arr_dailymotion['thumbnail_large_url'];
        return $thumb;
    }

    function getVimeoId($url)
    {
        if (preg_match('#(?:https?://)?(?:www.)?(?:player.)?vimeo.com/(?:[a-z]*/)*([0-9]{6,11})[?]?.*#', $url, $m)) {
            return $m[1];
        }
        return false;
    }

    function getVimeoThumb($id)
    {
        $arr_vimeo = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$id.php"));
//        return $arr_vimeo[0]['thumbnail_small']; // returns small thumbnail
        // return $arr_vimeo[0]['thumbnail_medium']; // returns medium thumbnail
         return $arr_vimeo[0]['thumbnail_large']; // returns large thumbnail
    }

    function extractVideoID($url){
        $regExp = "/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/";
        preg_match($regExp, $url, $video);
        return $video[7];
    }

    function getYouTubeThumbnailImage($video_id) {
        return "http://i3.ytimg.com/vi/$video_id/hqdefault.jpg"; //pass 0,1,2,3 for different sizes like 0.jpg, 1.jpg
    }

    public function newIngaUploadImage(Request $request) {
        if ($request->hasFile('image')) {
            $image = array('image' => $request->file('image'));
            $validator = Validator::make($image, [
                'image' => 'image|mimes:jpeg,png,jpg|max:1500',
            ]);
            if ($validator->fails()) {
                return Response::json(['error'=>''],500);
            } else {
                if ($request->file('image')->isValid()) {
                    $filename = Auth::user()->id.'-'.date('Ymdhis').'-'.rand(0,100000). '.' . $image["image"]->getClientOriginalExtension();
                    Image::make($image["image"])->save(public_path('/uploads/posts/' . $filename));
                    $width = Image::make(public_path('/uploads/posts/' . $filename))->width();
                    if ($width>1600) {
                        Image::make($image["image"])->widen(1600)->save(public_path('/uploads/posts/' . $filename));
                    }

                    if ($width>800) {
                        Image::make($image["image"])->widen(800)->save(public_path('/uploads/posts/thumbs/' . $filename));
                    } else {
                        Image::make($image["image"])->save(public_path('/uploads/posts/thumbs/' . $filename));
                    }
                    return Response::json(['success'=>true,'url'=>'/uploads/posts/thumbs/'.$filename]);
                } else {
                    return Response::json(['error'=>'']);
                }
            }
        } else {
            return Response::json(['error'=>'']);
        }
    }

    public function locationDelete(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return Response::json(['success' => false, 'message' => $validator->errors()]);
        }

        Auth::user()->locations()->where('id',$request->id)->delete();

        $locations = Auth::user()->locations()->get();
        return Response::json(['success'=>true,'locations'=>$locations]);
    }

    public function locationSave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_name' => 'required|string|max:50',
            'location_latitude' => 'required|max:50',
            'location_longitude' => 'required|max:50',
            'location_address' => 'required|string|max:250'
        ]);
        if ($validator->fails()) {
            return Response::json(['success' => false, 'message' => $validator->errors()]);
        }

        if ($request->true_address==false) {
            return Response::json(['success'=>false,'message'=>['error'=>['Lütfen konum bilgilerini kontrol ediniz.']]]);
        }

        $newLocation = new UserLocations();
        $newLocation->user_id = Auth::user()->id;
        $newLocation->share_location = $request->share_location;
        $newLocation->location_name = $request->location_name;
        $newLocation->location_latitude = $request->location_latitude;
        $newLocation->location_longitude = $request->location_longitude;
        $newLocation->location_address = $request->location_address;
        $newLocation->save();

        return Response::json(['success'=>true]);
    }

    public function locations() {
        $locations = Auth::user()->locations()->get();
        return Response::json(['locations'=>$locations]);
    }

    public function general() {
        $profile = Auth::user();
        $profile->privacies = $profile->privacies()->first();
        return Response::json(['profile'=>$profile]);
    }

    public function privacySave(Request $request) {
        $validator = Validator::make($request->all(), [
            'follow_privacy' => 'required|numeric',
            'post_privacy' => 'required|boolean',
            'location_privacy' => 'required|boolean',
            'message_privacy' => 'required|boolean',
            'email_search_privacy' => 'required|boolean',
            'mobile_search_privacy' => 'required|boolean'
        ]);
        if ($validator->fails()) {
            return Response::json(['success'=>false,'message'=>$validator->errors()]);
        }

        $profile_privacies = Auth::user()->privacies()->first();
        $profile_privacies->follow_privacy = $request->follow_privacy;
        $profile_privacies->post_privacy = $request->post_privacy;
        $profile_privacies->location_privacy = $request->location_privacy;
        $profile_privacies->message_privacy = $request->message_privacy;
        $profile_privacies->email_search_privacy = $request->email_search_privacy;
        $profile_privacies->mobile_search_privacy = $request->mobile_search_privacy;
        $profile_privacies->save();
        return Response::json(['success'=>true]);
    }

    public function generalSave(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'username' => 'required|string|alpha_dash|max:30|min:6',
            'mobile_number' => 'numeric|digits:11',
            'name' => 'required|string|max:30'
        ]);
        if ($validator->fails()) {
            return Response::json(['success'=>false,'message'=>$validator->errors()]);
        }
        if(User::where('username',$request->username)->where('id','<>',Auth::user()->id)->first()) {
            return Response::json(['success'=>false,'message'=>['error'=>['Bu kullanıcı adı başka bir üye tarafından kullanılıyor.']]]);
        }

        if(User::where('email',$request->email)->where('id','<>',Auth::user()->id)->first()) {
            return Response::json(['success'=>false,'message'=>['error'=>['Bu e-posta adresi başka bir üye tarafından kullanılıyor.']]]);
        }

        $profile = Auth::user();
        $profile->email = $request->email;
        $profile->username = $request->username;
        $profile->name = $request->name;
        $profile->mobile_number = $request->mobile_number;
        $profile->description = $request->personal_information;
        $profile->save();
        return Response::json(['success'=>true]);
    }

    public function logout() {
        Auth::logout();
        return redirect('/');
    }
}
