<?php

namespace App\DataTables;

use App\Models\Task;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class TaskDataTable extends DataTable
{
    protected mixed $listId;

    public function __construct($listId = null)
    {
        parent::__construct();
        $this->listId = $listId;
    }

    /**
     * @param QueryBuilder $query
     * @return EloquentDataTable
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('images', function (Task $entity) {
                return view('tasks.image-thumbnail', [
                    'task' => $entity
                ]);
            })
            ->addColumn('tags', function (Task $entity) {
                return view('tasks.tags', [
                    'tags' => $entity->tags()->get()
                ]);
            })
            ->addColumn('action', function (Task $entity) {
                return view('components.default-action', ['data' => [
                    'route' => '',
                    'entity' => $entity
                ]])->render();
            })
            ->setRowId('id');
    }

    /**
     * @param Task $model
     * @return QueryBuilder
     */
    public function query(Task $model): QueryBuilder
    {
        $query = $model->newQuery()->where('to_do_list_id', $this->listId);

        if (request()->has('tags')) {
            $tags = request('tags');
            $query->whereHas('tags', function ($q) use ($tags) {
                $q->whereIn('tags.id', $tags);
            });
        }

        return $query;
    }

    /**
     * @return HtmlBuilder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('entity-table')
            ->addTableClass('w-100')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('ftp')
            ->paging()
            ->orderBy(0)
            ->responsiveDetails(true)
            ->fixedColumns()
            ->selectStyleSingle();
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        $columns = config('datatables.tasks');

        $array[] = Column::make('id')->addClass('col-1 text-center');

        foreach ($columns as $key => $value) {
            $array[] = Column::computed($key)
                ->title($value['title'])
                ->orderable()
                ->searchable();
        }

        $array[] = Column::computed('tags')
            ->addClass('col-auto')
            ->searchable();

        $array[] = Column::computed('images')
            ->addClass('col-auto')
            ->searchable();

        $array[] = Column::computed('action')
            ->searchable(false)
            ->width(60)
            ->addClass('text-center')
            ->title('');

        return $array;
    }

    /**
     * @return string
     */
    protected function filename(): string
    {
        return 'Task_' . date('YmdHis');
    }
}
