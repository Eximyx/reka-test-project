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
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
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
     * Get the query source of dataTable.
     */
    public function query(Task $model): QueryBuilder
    {
        return $model->newQuery()->where('to_do_list_id', $this->listId);
    }

    /**
     * Optional method if you want to use the html builder.
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
            ->fixedColumns("action")
            ->selectStyleSingle();
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        $columns = config("datatables.tasks");

        $array[] = Column::make('id')->addClass('col-1 text-center');

        foreach ($columns as $key => $value) {
            $array[] = Column::computed($key)
                ->title($value["title"])
                ->orderable()
                ->searchable();
        }

        $array[] = Column::computed('tags')
            ->searchable(false);

        $array[] = Column::computed('action')
            ->searchable(false)
            ->width(60)
            ->addClass('text-center')
            ->title("");

        return $array;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Task_' . date('YmdHis');
    }
}
