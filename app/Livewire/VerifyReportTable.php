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

final class VerifyReportTable extends PowerGridComponent
{
    public string $tableName = 'verifyReportTable';

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
            ->whereNotNull('approval_id')
            ->whereHas('user', function ($query) {
                $query->where('unit_component', Auth::user()->superior_role);
            })
            ->with('user')
            ->select('report_num', 'status', 'user_id', 'travel_order_id', 'approval_id')
            ->selectRaw('MIN(id) as id')
            ->selectRaw('MIN(updated_at) as approved_at')
            ->selectRaw('COUNT(*) as reports_count')
            ->groupBy('report_num', 'status', 'user_id', 'travel_order_id', 'approval_id');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('approval_id')
            ->add('report_num')
            ->add('user_name', fn($row) => $row->user->name ?? 'Unknown')
            ->add('travel_order_id')
            ->add('approved_at_formatted', fn($row) => $row->approved_at ? Carbon::parse($row->approved_at)->format('F j, Y') : 'N/A');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()
                ->searchable(),

            Column::make('Approval ID', 'approval_id')
                ->sortable()
                ->searchable(),

            Column::make('Travel Order ID', 'travel_order_id')
                ->sortable()
                ->searchable(),

            Column::make('Submitted By', 'user_name')
                ->sortable()
                ->searchable(),

            Column::make('Approved At', 'approved_at_formatted')
                ->sortable()
                ->searchable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::datepicker('updated_at'),
        ];
    }

    #[\Livewire\Attributes\On('view')]
    public function view($rowId): void
    {
        // Get the report and dispatch event to generate PDF
        $report = BackToOfficeReport::find($rowId);
        if ($report) {
            $this->dispatch('generateVerifyReport', reportNum: $report->report_num);
        }
    }

    public function actions(BackToOfficeReport $row): array
    {
        return [
            Button::add('view')
                ->slot('View Report')
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('view', ['rowId' => $row->id])
        ];
    }
}
