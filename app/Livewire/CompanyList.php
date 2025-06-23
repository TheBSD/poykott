<?php

namespace App\Livewire;

use App\Models\Company;
use App\Models\Tag;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class CompanyList extends Component
{
    use WithPagination;

    public $search = '';

    public $filter = '';

    public $order = '';

    protected $queryString = ['search', 'filter', 'order'];

    #[Computed]
    public function companiesTags()
    {
        return Cache::remember('company-tags-filter', now()->addMinute(), function () {
            return Tag::query()->withCount('companies')
                ->has('companies')
                ->orderBy('companies_count', 'desc')
                ->get();
        });
    }

    public function render()
    {
        $query = Company::query()
            ->with([
                'media',
                'tagsRelation' => function ($query): void {
                    $query->select('tags.id', 'name')->limit(3);
                },
            ])
            ->approved()
            ->when($this->search, function ($query): void {
                $query->where(function ($query): void {
                    $query->where('name', 'like', "%{$this->search}%")
                        ->orWhereHas('tagsRelation', function ($query): void {
                            $query->where('name', 'like', "%{$this->search}%");
                        });
                });
            });
        // ->when($this->filter, function ($query): void {
        //     $query->whereHas('tagsRelation', function ($query): void {
        //         $query->where('tags.id', $this->filter);
        //     });
        // })
        // ->when($this->order, function ($query): void {
        //     $query->orderBy('name', $this->order);
        // })

        $companies = $query->simplePaginate(
            20,
            ['companies.id', 'name', 'description', 'short_description', 'slug', 'image_path']
        );

        return view('livewire.company-list', [
            'companies' => $companies,
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
