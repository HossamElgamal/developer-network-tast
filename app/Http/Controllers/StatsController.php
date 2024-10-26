<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StatsController extends Controller
{
    public function index()
    {

        $stats = Cache::remember('stats', 60 * 60, function () {
            return $this->generateStats();
        });

        return response()->json($stats);
    }

    private function generateStats(): array
    {

        $totalUsers = User::count();
        $totalPosts = Post::count();
        $usersWithNoPosts = User::doesntHave('posts')->count();

        return [
            'total_users' => $totalUsers,
            'total_posts' => $totalPosts,
            'users_with_no_posts' => $usersWithNoPosts,
        ];
    }
}
