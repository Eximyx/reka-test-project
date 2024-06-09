<?php

namespace App\Policies;

use App\Models\ToDoList;
use App\Models\User;

class ToDoListPolicy
{
    /**
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * @param User $user
     * @param ToDoList $toDoList
     * @return bool
     */
    public function edit(User $user, ToDoList $toDoList): bool
    {
        return $user->id === $toDoList->user_id;
    }

    /**
     * @param User $user
     * @param ToDoList $toDoList
     * @return bool
     */
    public function view(User $user, ToDoList $toDoList): bool
    {
        return $user->id === $toDoList->user_id;
    }

    /**
     * @param User $user
     * @param ToDoList $toDoList
     * @return bool
     */
    public function update(User $user, ToDoList $toDoList): bool
    {
        return $user->id === $toDoList->user_id;
    }

    /**
     * @param User $user
     * @param ToDoList $toDoList
     * @return bool
     */
    public function delete(User $user, ToDoList $toDoList): bool
    {
        return $user->id === $toDoList->user_id;
    }
}
