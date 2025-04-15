<?php

namespace App\Livewire;

use App\Models\PcMov;
use App\Models\PcNfEnt;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Vendas extends Component
{
    #[Validate(['required', 'numeric'])]
    public $numtransent;
    public $search_numtransent = '';
    #[Validate(['required', 'date'])]
    public $datai;
    #[Validate(['required', 'date'])]
    public $dataf;

    #[Computed]
    public function nfEntradas()
    {
        return PcNfEnt::where('numtransent', 'like', strtoupper($this->search_numtransent))
            ->when(is_numeric($this->search_numtransent), function ($query) {
                return $query->orWhere('numtransent', $this->search_numtransent);
            })
            ->join('pcfornec', 'pcfornec.codfornec', '=', 'pcnfent.codfornec')
            ->limit(50)
            ->orderBy('dtemissao', 'desc')
            ->get();
    }

    public function submit()
    {
        $this->validate(); // Garante que os campos obrigatórios estão preenchidos corretamente

        $pcmov = PcMov::where('numtransent', $this->numtransent)
            ->pluck('codprod')
            ->toArray();

        return redirect()->to(route('vendas.show', [
            'numtransent' => $this->numtransent,
            'pcmov' => implode(',', $pcmov),
            'datai' => $this->datai,
            'dataf' => $this->dataf,
        ]));
    }

    public function mount()
    {
        $this->datai = now()->format('Y-m-d');
        $this->dataf = now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.venda');
    }
}
