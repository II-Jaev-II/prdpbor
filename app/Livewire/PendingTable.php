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
            ->whereIn('status', ['Pending', 'For Revision'])
            ->select('report_num', 'status', 'user_id', 'travel_order_id')
            ->selectRaw('MIN(id) as id')
            ->selectRaw('MIN(start_date) as start_date')
            ->selectRaw('MIN(end_date) as end_date')
            ->selectRaw('MIN(created_at) as created_at')
            ->selectRaw('COUNT(*) as reports_count')
            ->groupBy('report_num', 'status', 'user_id', 'travel_order_id');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('travel_order_id')
            ->add('start_date_formatted', fn($row) => $row->start_date ? Carbon::parse($row->start_date)->format('F j, Y') : 'N/A')
            ->add(
                'end_date_formatted',
                fn($row) =>
                $row->end_date ? Carbon::parse($row->end_date)->format('F j, Y') : ($row->start_date ? Carbon::parse($row->start_date)->format('F j, Y') : 'N/A')
            )
            ->add('status')
            ->add('created_at_formatted', fn($row) => $row->created_at ? Carbon::parse($row->created_at)->format('F j, Y') : 'N/A')
            ->add('days_pending', function($row) {
                if (!$row->created_at) return '-';
                $createdAt = Carbon::parse($row->created_at);
                $now = Carbon::now();
                $days = (int) floor($createdAt->diffInDays($now));

                if ($days >= 1) {
                    return $days . ' day' . ($days > 1 ? 's' : '');
                }
                return '-';
            })
            ->add('reports_count', fn($row) => $row->reports_count ?? 1);
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
        $isForRevision = $row->status === 'For Revision';
        
        $viewButtonSlot = 'View/Edit';
        if ($isForRevision) {
            $viewButtonSlot .= '
                <span class="absolute -top-1 -right-1 flex size-3">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex size-3 rounded-full bg-red-500"></span>
                </span>
                <div class="absolute bottom-full right-0 mb-2 px-2 py-1 text-sm text-white bg-gray-900 rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-10">
                    This report needs revision.
                    <div class="absolute top-full right-2 w-0 h-0 border-l-2 border-r-2 border-t-2 border-transparent border-t-gray-900"></div>
                </div>
            ';
        }
        
        $viewButtonClass = 'relative group pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700';

        return [
            Button::add('view')
                ->slot($viewButtonSlot)
                ->id()
                ->class($viewButtonClass)
                ->dispatch('viewReports', ['reportNum' => $row->report_num]),

            Button::add('delete')
                ->slot('Delete')
                ->id()
                ->class('pg-btn-white dark:ring-red-600 dark:border-red-600 dark:hover:bg-red-700 dark:ring-offset-red-800 dark:text-red-300 dark:bg-red-700 text-red-600 border-red-600 hover:bg-red-50')
                ->dispatch('deleteReports', ['reportNum' => $row->report_num])
        ];
    }
}
