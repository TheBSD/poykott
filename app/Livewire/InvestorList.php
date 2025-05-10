<?php

namespace App\Livewire;

use App\Models\Investor;
use Livewire\Component;
use Livewire\WithPagination;

class InvestorList extends Component
{
    use WithPagination;

    public $search = '';

    public $order = '';

    protected $queryString = ['search', 'order'];

    public function render()
    {
        $investors = Investor::query()
            ->with(['media'])
            ->approved()
            ->when($this->search, function ($query): void {
                $query->where(function ($query): void {
                    $query->where('name', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->when($this->order, function ($query): void {
                $query->orderBy('name', $this->order);
            })
            ->simplePaginate(20, ['investors.id', 'name', 'slug']);

        return view('livewire.investor-list', [
            'investors' => $investors,
        ]);
    }

    /**
     * =====================
     * Resetting pagination after searching, filtering, or ordering
     * =====================
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilter(): void
    {
        $this->resetPage();
    }

    public function updatingOrder(): void
    {
        $this->resetPage();
    }
}
