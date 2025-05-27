<?php

namespace App\Livewire;

use App\Models\Alternative;
use App\Models\Tag;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class AlternativeList extends Component
{
    use WithPagination;

    public $search = '';

    public $filter = '';

    public $order = '';

    protected $queryString = ['search', 'filter', 'order'];

    #[Computed]
    public function alternativesTags()
    {
        return Cache::remember('alternatives-tags-filter', now()->addMinute(), function () {
            return Tag::query()->withCount('alternatives')
                ->has('alternatives')
                ->orderBy('alternatives_count', 'desc')
                ->get();
        });
    }

    public function render()
    {
        $query = Alternative::query()
            ->with([
                'media',
                'tagsRelation' => function ($query): void {
                    $query->select('tags.id', 'name')->limit(3);
                },
                'companies' => function ($query): void {
                    $query->select(['id', 'name', 'slug']);
                },
            ])
            ->approved()
            ->when($this->search, function ($query): void {
                $query->where(function ($query): void {
                    $query
                        ->where('name', 'like', "%{$this->search}%")
                        ->orWhereHas('tagsRelation', function ($query): void {
                            $query->where('name', 'like', "%{$this->search}%");
                        })
                        ->orWhereHas('companies', function ($query): void {
                            $query->where('name', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->filter, function ($query): void {
                $query->whereHas('tagsRelation', function ($query): void {
                    $query->where('tags.id', $this->filter);
                });
            })
            ->when($this->order, function ($query): void {
                $query->orderBy('name', $this->order);
            });

        $alternatives = $query->simplePaginate(20,
            ['alternatives.id', 'name', 'description', 'slug', 'image_path']);

        return view('livewire.alternative-list', [
            'alternatives' => $alternatives,
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
