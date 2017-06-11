<?php

namespace Sweet\Http\Controllers;

use Illuminate\Http\Request;

use Sweet\Http\Requests;

use Sweet\User;
use Sweet\Http\Controllers\AuthController;


use App\Post;

//ProductExhibitionAuditHdr
class PeaController extends Controller
{
    
    function index(Posts $posts){
        /*
        $posts = Post::latest()
         ->filter(request(['month', 'year']))
         ->get();
*/
        $posts = $posts->all();

    	return view('posts.index', compact('posts'));
	}
}
