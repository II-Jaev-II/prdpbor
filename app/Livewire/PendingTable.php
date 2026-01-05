<?php

namespace App\Livewire;

use App\Models\BackToOfficeReport;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
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
        return BackToOfficeReport::query()
            ->where('user_id', Auth::id())
            ->where('status', 'Pending');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('travel_order_id')
            ->add('start_date_formatted', fn(BackToOfficeReport $model) => Carbon::parse($model->start_date)->format('F j, Y'))
            ->add(
                'end_date_formatted',
                fn(BackToOfficeReport $model) =>
                $model->end_date ? Carbon::parse($model->end_date)->format('F j, Y') : Carbon::parse($model->start_date)->format('F j, Y')
            )
            ->add('accomplishment')
            ->add('status')
            ->add('created_at_formatted', fn(BackToOfficeReport $model) => Carbon::parse($model->created_at)->format('F j, Y'))
            ->add('days_pending', function(BackToOfficeReport $model) {
                $createdAt = Carbon::parse($model->created_at);
                $now = Carbon::now();
                $days = (int) floor($createdAt->diffInDays($now));
                
                if ($days >= 1) {
                    return $days . ' day' . ($days > 1 ? 's' : '');
                }
                return '-';
            });
    }

    public function columns(): array
    {
        return [
            Column::make('Travel Order ID', 'travel_order_id')
                ->sortable()
                ->searchable(),

            Column::make('Start Date', 'start_date_formatted', 'start_date')
                ->sortable(),

            Column::make('End Date', 'end_date_formatted', 'end_date')
                ->sortable(),

            Column::make('Status', 'status')
                ->sortable()
                ->searchable(),

            Column::make('Submitted At', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::make('Days Pending', 'days_pending', 'created_at')
                ->sortable(),

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
                ->dispatch('editReport', ['rowId' => $row->id]),

            Button::add('delete')
                ->slot('Delete')
                ->id()
                ->class('pg-btn-white dark:ring-red-600 dark:border-red-600 dark:hover:bg-red-700 dark:ring-offset-red-800 dark:text-red-300 dark:bg-red-700 text-red-600 border-red-600 hover:bg-red-50')
                ->dispatch('deleteReport', ['rowId' => $row->id])
        ];
    }
}
