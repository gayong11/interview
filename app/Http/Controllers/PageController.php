<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    /**
     * 主页
     *
     * @param Request $request
     *
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function root(Request $request)
    {
        $user = $request->user();
        $complete = Cache::has('user:id:' . $user->id . ':complete');
        if ($complete) {
            $topic = [];
        } else {
            $currentId = Cache::get('user:id:' . $user->id . ':current_topic_id');
            $topic = Topic::query()
                ->when($currentId, function ($query) use ($currentId) {
                    return $query->where('id', $currentId);
                })
                ->inRandomOrder()
                ->with('options')
                ->first();
            if (!$currentId) {
                $topicId = [$topic->id];
                Cache::forever('user:id:' . $user->id . ':topic_id', $topicId);
                Cache::forever('user:id:' . $user->id . ':current_topic_id', $topic->id);
            }
        }
        $topicId = Cache::get('user:id:' . $user->id . ':topic_id');
        $count = Topic::query()->count('*');
        $countTopic = count($topicId);

        return view('pages.root', compact('topic', 'count', 'countTopic'));
    }

    /**
     * 邮箱验证
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function emailVerifyNotice(Request $request)
    {
        return view('pages.email_verify_notice');
    }

    /**
     * 重新开始答题
     *
     * @param Request $request
     *
     * @return array
     */
    public function reset(Request $request)
    {
        $user = $request->user();
        Cache::forget('user:id:' . $user->id . ':topic_id');
        Cache::forget('user:id:' . $user->id . ':complete');
        Cache::forget('user:id:' . $user->id . ':current_topic_id');

        return [];
    }

    /**
     * 下一题
     *
     * @param Request $request
     *
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function next(Request $request)
    {
        $user = $request->user();
        $topicId = Cache::get('user:id:' . $user->id . ':topic_id');
        $topic = Topic::query()
            ->whereNotIn('id', $topicId)
            ->inRandomOrder()
            ->with('options')
            ->first();
        if ($topic) {
            array_push($topicId, $topic->id);
            Cache::forever('user:id:' . $user->id . ':topic_id', $topicId);
            Cache::forever('user:id:' . $user->id . ':current_topic_id', $topic->id);
        } else {
            Cache::forever('user:id:' . $user->id . ':complete', true);
        }

        return $topic;
    }

}
