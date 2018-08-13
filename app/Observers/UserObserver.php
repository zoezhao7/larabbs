<?php

namespace App\Observers;

use App\Models\User;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class UserObserver
{
    public function saving(User $user)
    {
        // 如果不是 `http` 子串开头，那就是从后台上传的，需要补全 URL
        if ( ! starts_with($user->avatar, 'http')) {

            // 拼接完整的 URL
            $user->avatar = config('app.url') . "/uploads/images/avatars/" . $user->avatar;
        }

    }


}