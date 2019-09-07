<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserFollowGame;
use App\UserFollowUser;
use App\Game;
use App\News;
use App\User;
use App\Like;
use App\Post;
use Illuminate\Support\Facades\DB;

class FeedController extends Controller
{
    public function get_modal_info($post_id){
        $post_info = DB::table('posts')
        ->leftJoin('users', 'users.id', '=', 'posts.user_id') 
        ->select('users.username', 'users.icon', 'users.name', 'posts.post', 'posts.id as post_id', 'posts.created_at', 'users.id')
        ->where('posts.id', '=', $post_id)->get();

        
        $comments = DB::table('comments')
        ->leftJoin('users', 'users.id', '=', 'comments.user_id')
        ->where('post_id', '=', $post_id)
        ->select('comments.comment', 'comments.post_id', 'users.name', 'users.username', 'users.icon', 'users.id')
        ->get();

        return response()->json([
            'post_info' => $post_info,
            'comments' => $comments
        ],200);
    }

    public function user_feed($id){

        $posts = DB::table('userfollowuser')
        ->leftJoin('posts', 'userfollowuser.user_id_followed', '=', 'posts.user_id')
        ->leftJoin('users', 'users.id', '=', 'userfollowuser.user_id_followed')
        ->leftJoin('likes', 'likes.post_id', '=', 'posts.id')
        ->where('userfollowuser.user_id', '=', $id)
        ->select(DB::raw('COUNT(likes.id) as num_likes'), 'users.username', 'users.icon', 'users.name', 'posts.post', 'posts.id as post_id', 'posts.created_at', 'users.id')
        ->groupBy('users.username', 'users.icon', 'users.name', 'posts.post', 'posts.id', 'posts.created_at', 'users.id')
        ->orderBy('posts.created_at', 'desc')
        ->simplePaginate(3);
        
        $news = DB::table('userfollowgames')
        ->leftJoin('games', 'games.id', '=', 'userfollowgames.game_id')
        ->leftJoin('news', 'news.tag', '=', 'games.tag')
        ->where('userfollowgames.user_id', '=', $id)
        ->orderBy('news.date', 'desc')
        ->simplePaginate(2);
        
        $followed_users = DB::table('users')
        ->leftJoin('userfollowuser', 'users.id', '=', 'userfollowuser.user_id_followed')
        ->where('userfollowuser.user_id', '=', $id)
        ->select('users.summonerName', 'users.id', 'users.username')
        ->simplePaginate(2);

        $list_id = [];
        $liked_posts = Like::where('user_id', '=', $id)->get('post_id');
        foreach($liked_posts as $like){
            $list_id [] = $like->post_id;
        }
        
        return response()->json([
            'news' => $news,
            'posts' => $posts,
            'followed_users' => $followed_users,
            'liked_posts' => $list_id
        ], 200);

    }
}
