<?php

namespace App\DataTables;

use App\Models\ToDoList;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ToDoListDataTable extends DataTable
{
    protected mixed $userId;

    public function __construct($userId = null)
    {
        parent::__construct();
        $this->userId = $userId;
    }

    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function (ToDoList $entity) {
                return view('components.default-action', ['data' => [
                    'route' => route('tasks.index', ['toDoList' => $entity->id]),
                    'entity' => $entity
                ]])->render();
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ToDoList $model): QueryBuilder
    {
        return $model->newQuery()->where('user_id', $this->userId);
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
            ->paging(true)
            ->orderBy(0, "desc")
            ->responsiveDetails(true)
            ->fixedColumns("action")
            ->selectStyleSingle();
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        $columns = config("datatables.lists");

        $array[] = Column::make('id')->addClass('col-1 text-center');

        foreach ($columns as $key => $value) {
            $array[] = Column::computed($key)
                ->title($value["title"])
                ->orderable()
                ->searchable();
        }

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
        return 'ToDoList_' . date('YmdHis');
    }
}
