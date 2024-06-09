<?php

namespace App\Http\Controllers;

use App\DataTables\TaskDataTable;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\ToDoList;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Task::class, 'task');
    }

    /**
     * @param ToDoList $toDoList
     * @return mixed
     * @throws AuthorizationException
     */
    public function index(ToDoList $toDoList): mixed
    {
        $this->authorize('view', $toDoList);

        $dataTable = app(TaskDataTable::class, [
            'listId' => $toDoList->id
        ]);

        $tags = $toDoList->tasks()
            ->join('tag_task', 'tasks.id', '=', 'tag_task.task_id')
            ->join('tags', 'tag_task.tag_id', '=', 'tags.id')
            ->select('tags.*')
            ->distinct()
            ->get();

        return $dataTable->render('tasks.index', ['tags' => $tags]);
    }

    /**
     * @param StoreTaskRequest $request
     * @param ToDoList $toDoList
     * @return JsonResponse
     */
    public function store(StoreTaskRequest $request, ToDoList $toDoList): JsonResponse
    {
        Task::query()->create(
            array_merge($request->validated(), [
                'to_do_list_id' => $toDoList->id
            ])
        );

        return response()->json([
            'success' => true,
            'data' => [
                'message' => __('models.lists.messages.store.success')
            ]
        ]);
    }

    /**
     * @param ToDoList $toDoList
     * @param Task $task
     * @return JsonResponse
     */
    public function edit(ToDoList $toDoList, Task $task): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'message' => __('models.list.messages.find.success'),
                'entity' => $task,
            ]
        ]);
    }

    /**
     * @param UpdateTaskRequest $request
     * @param ToDoList $toDoList
     * @param Task $task
     * @return JsonResponse
     */
    public function update(UpdateTaskRequest $request, ToDoList $toDoList, Task $task): JsonResponse
    {
        $task->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => [
                'message' => __('models.tasks.messages.update.success'),
                'entity' => $task
            ]
        ]);
    }

    /**
     * @param ToDoList $toDoList
     * @param Task $task
     * @return JsonResponse
     */
    public function destroy(ToDoList $toDoList, Task $task): JsonResponse
    {
        $task->delete();

        return response()->json([
            'success' => true,
            'data' => [
                'message' => __('models.list.messages.delete.success')
            ]
        ]);
    }
}
