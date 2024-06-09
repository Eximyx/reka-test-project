<?php

namespace App\Http\Controllers;

use App\DataTables\ToDoListDataTable;
use App\Http\Requests\StoreToDoListRequest;
use App\Http\Requests\UpdateToDoListRequest;
use App\Models\ToDoList;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ToDoListController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ToDoList::class, 'toDoList');
    }

    /**
     * @return mixed
     */
    public function index(): mixed
    {
        $dataTable = app(ToDoListDataTable::class, [
            'userId' => Auth::user()->id
        ]);

        return $dataTable->render('todolist.index');
    }

    /**
     * @param StoreToDoListRequest $request
     * @return JsonResponse
     */
    public function store(StoreToDoListRequest $request): JsonResponse
    {
        ToDoList::query()->create($request->validated());

        return response()->json([
            'success' => true,
            'data' => [
                'message' => __('models.lists.messages.store.success')
            ]
        ]);
    }

    /**
     * @param ToDoList $toDoList
     * @return JsonResponse
     */
    public function edit(ToDoList $toDoList): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'message' => __('models.list.messages.find.success'),
                'entity' => $toDoList,
            ]
        ]);
    }

    /**
     * @param UpdateToDoListRequest $request
     * @param ToDoList $toDoList
     * @return JsonResponse
     */
    public function update(UpdateToDoListRequest $request, ToDoList $toDoList): JsonResponse
    {
        $toDoList->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => [
                'message' => __('models.list.messages.update.success'),
                'entity' => $toDoList
            ]
        ]);
    }

    /**
     * @param ToDoList $toDoList
     * @return JsonResponse
     */
    public function destroy(ToDoList $toDoList): JsonResponse
    {
        $toDoList->delete();

        return response()->json([
            'success' => true,
            'data' => [
                'message' => __('models.list.messages.delete.success')
            ]
        ]);
    }
}
