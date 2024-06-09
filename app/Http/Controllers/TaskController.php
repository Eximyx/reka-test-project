<?php

namespace App\Http\Controllers;

use App\DataTables\TaskDataTable;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\ToDoList;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

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

        return $dataTable->render('tasks.index', ['toDoList' => $toDoList, 'tags' => $tags]);
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
                'message' => 'Задача успешно найдена',
                'entity' => $task,
                'tags' => $task->tags
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
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $originalFile = $request->file('image');

            $originalFile->store('images/tasks/' . $task->id, 'public');

            $data['image'] = $originalFile->hashName();

            $thumbnailPath = 'images/tasks/' . $task->id . '/thumbnail_' . $originalFile->hashName();
            $thumbnailImage = Image::read($originalFile)->resize(150, 150)->encode();

            Storage::disk('public')->put($thumbnailPath, $thumbnailImage);

            if ($task->image) {
                $pathToImage = explode('/', $task->image);

                $imageName = array_pop($pathToImage);

                Storage::disk('public')->delete($task->image);
                Storage::disk('public')->delete(str_replace($imageName, 'thumbnail_' . $imageName, $imageName));
            }
        }

        $task->update($data);

        if ($request->has('tags')) {
            $task->tags()->sync($request->input('tags'));
        }

        return response()->json([
            'success' => true,
            'data' => [
                'message' => 'Вы успешно обновили информацию о задаче',
                'entity' => $task,
                'tags' => $task->tags
            ]
        ]);
    }

    /**
     * @param StoreTaskRequest $request
     * @param ToDoList $toDoList
     * @return JsonResponse
     */
    public function store(StoreTaskRequest $request, ToDoList $toDoList): JsonResponse
    {
        $data = $request->validated();

        $task = Task::query()->create(
            array_merge($data, [
                'to_do_list_id' => $toDoList->id
            ])
        );

        if ($request->hasFile('image')) {
            $originalFile = $request->file('image');

            $originalFile->store('images/tasks/' . $task->id, 'public');

            $data['image'] = $originalFile->hashName();

            $thumbnailPath = 'images/tasks/' . $task->id . '/thumbnail_' . $originalFile->hashName();
            $thumbnailImage = Image::read($originalFile)->resize(150, 150)->encode();

            Storage::disk('public')->put($thumbnailPath, $thumbnailImage);
            $task->update(['image' => $data['image']]);
        }

        if ($request->has('tags')) {
            $task->tags()->sync($request->input('tags'));
        }

        return response()->json([
            'success' => true,
            'data' => [
                'message' => 'Вы успешно добавили задачу',
                'task' => $task
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
        Storage::delete($task->getPathToImage() . '/' . $task->image);
        Storage::delete($task->getPathToImage() . '/thumbnail_' . $task->image);

//        $task->delete();
        logger()->info('Deleting image:', ['path' => $task->getPathToImage() . '/' . $task->image]);

        return response()->json([
            'success' => true,
            'data' => [
                'message' => 'Вы успешно удалили задачу'
            ]
        ]);
    }
}
