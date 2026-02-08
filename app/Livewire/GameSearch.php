<?php

namespace App\Livewire;

use App\Services\SteamApiService;
use Livewire\Component;

class GameSearch extends Component
{
    public string $query = '';

    public array $results = [];

    public bool $showResults = false;

    public function updatedQuery(): void
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            $this->showResults = false;

            return;
        }

        $steamApi = app(SteamApiService::class);
        $this->results = array_slice($steamApi->searchGames($this->query), 0, 8);
        $this->showResults = true;
    }

    public function hideResults(): void
    {
        $this->showResults = false;
    }

    public function render()
    {
        return view('livewire.game-search');
    }
}
