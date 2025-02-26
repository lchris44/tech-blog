<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class UserService
{
    /**
     * Create a new user.
     *
     * @return void
     *
     * @throws Exception
     */
    public function createUser(array $data)
    {
        DB::transaction(function () use ($data) {
            User::create($data);
        });
    }

    /**
     * Update an existing user.
     *
     * @return void
     *
     * @throws Exception
     */
    public function updateUser(User $user, array $data)
    {
        DB::transaction(function () use ($user, $data) {
            $user->update($data);
        });
    }

    /**
     * Delete a user.
     *
     * @return void
     *
     * @throws Exception
     */
    public function deleteUser(User $user)
    {
        DB::transaction(function () use ($user) {
            $user->delete();
        });
    }
}
