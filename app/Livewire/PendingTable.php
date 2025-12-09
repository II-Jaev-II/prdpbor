<?php

namespace App\Livewire;

use App\Models\BackToOfficeReport;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class PendingTable extends PowerGridComponent
{
    public string $tableName = 'pendingTable';

    public function setUp(): array
    {
        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return BackToOfficeReport::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('report_num')
            ->add('start_date_formatted', fn(BackToOfficeReport $model) => Carbon::parse($model->start_date)->format('F j, Y'))
            ->add(
                'end_date_formatted',
                fn(BackToOfficeReport $model) =>
                $model->end_date ? Carbon::parse($model->end_date)->format('F j, Y') : Carbon::parse($model->start_date)->format('F j, Y')
            )
            ->add('purpose')
            ->add('place')
            ->add('accomplishment')
            ->add('status');
    }

    public function columns(): array
    {
        return [
            Column::make('Report #', 'report_num')
                ->sortable()
                ->searchable(),

            Column::make('Start Date', 'start_date_formatted', 'start_date')
                ->sortable(),

            Column::make('End Date', 'end_date_formatted', 'end_date')
                ->sortable(),

            Column::make('Purpose', 'purpose')
                ->sortable()
                ->searchable(),

            Column::make('Place', 'place')
                ->sortable()
                ->searchable(),

            Column::make('Status', 'status')
                ->sortable()
                ->searchable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::datepicker('start_date', 'end_date')
                ->params([
                    'mode' => 'range',
                    'enableTime' => false,
                    'altInput' => true,
                    'altFormat' => 'F j, Y'
                ]),
        ];
    }

    public function actions(BackToOfficeReport $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit')
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('editReport', ['rowId' => $row->id])
        ];
    }
}