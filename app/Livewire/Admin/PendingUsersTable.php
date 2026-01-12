<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class PendingUsersTable extends PowerGridComponent
{
    public string $tableName = 'pendingUsersTable';

    protected function getListeners()
    {
        return array_merge(
            parent::getListeners(),
            [
                'userAssigned' => '$refresh',
            ]
        );
    }

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
        return User::query()
            ->where('is_approved', false);
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('email')
            ->add('designation')
            ->add('created_at')
            ->add('created_at_formatted', fn($row) => Carbon::parse($row->created_at)->format('M d, Y'));
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable(),

            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Email', 'email')
                ->sortable()
                ->searchable(),

            Column::make('Designation', 'designation')
                ->sortable()
                ->searchable(),

            Column::make('Registered At', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::datepicker('created_at'),
        ];
    }

    public function actions(User $row): array
    {
        return [
            Button::add('assign')
                ->slot('Assign Unit')
                ->class('bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors')
                ->dispatch('openAssignModal', ['userId' => $row->id])
        ];
    }
}
