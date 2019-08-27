<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserFollowGame;
use App\UserFollowUser;
use App\Game;
use App\News;
use App\User;
use App\Post;
use Illuminate\Support\Facades\DB;

class FeedController extends Controller
{
    public function user_feed($id){

        $posts = DB::table('userfollowuser')
        ->leftJoin('posts', 'userfollowuser.user_id_followed', '=', 'posts.user_id')
        ->leftJoin('users', 'users.id', '=', 'userfollowuser.user_id_followed')
        ->where('userfollowuser.user_id', '=', $id)
        ->select('users.username', 'users.icon', 'users.name', 'posts.post', 'posts.created_at')
        ->orderBy('posts.created_at', 'desc')
        ->simplePaginate(3);
        
        $news = DB::table('userfollowgames')
        ->leftJoin('games', 'games.id', '=', 'userfollowgames.game_id')
        ->leftJoin('news', 'news.tag', '=', 'games.tag')
        ->where('userfollowgames.user_id', '=', $id)
        ->orderBy('news.date', 'desc')
        ->simplePaginate(2);
        
        return response()->json([
            'news' => $news,
            'posts' => $posts
        ], 200, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);

    }
}
