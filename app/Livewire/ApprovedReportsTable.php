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

final class ApprovedReportsTable extends PowerGridComponent
{
    public string $tableName = 'approvedReportsTable';

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
            ->where('status', 'Approved')
            ->where('user_id', Auth::id())
            ->with('user')
            ->select('report_num', 'status', 'user_id')
            ->selectRaw('MIN(id) as id')
            ->selectRaw('COUNT(*) as reports_count')
            ->groupBy('report_num', 'status', 'user_id');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('report_num')
            ->add('user_name', fn($row) => $row->user->name ?? 'Unknown')
            ->add('status')
            ->add('reports_count', fn($row) => $row->reports_count ?? 1);
    }

    public function columns(): array
    {
        return [
            Column::make('Report Number', 'report_num')
                ->sortable()
                ->searchable(),

            Column::make('Submitted By', 'user_name')
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
            Filter::datepicker('start_date'),
            Filter::datepicker('end_date'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        // Get the report_num from the rowId
        $report = BackToOfficeReport::find($rowId);
        if ($report) {
            $this->dispatch('viewReports', reportNum: $report->report_num);
        }
    }

    public function actions(BackToOfficeReport $row): array
    {
        return [
            Button::add('edit')
                ->slot('View')
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id])
        ];
    }
}
