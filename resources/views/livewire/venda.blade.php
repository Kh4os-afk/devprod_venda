<div class="space-y-6">
    <div>
        <flux:heading size="xl" level="1">Relatório de Vendas</flux:heading>
        <flux:text class="mb-6 mt-2 text-base">Análise detalhada do fluxo de venda por nota de entrada.</flux:text>
        <flux:separator variant="subtle"/>
    </div>
    <flux:card class="space-y-6">
        <form class="grid grid-cols-12 gap-2" wire:submit="submit">
            <div class="col-span-6">
                <flux:input type="date" label="Data Inicial" wire:model="datai" />
            </div>

            <div class="col-span-6">
                <flux:input type="date" label="Data Final" wire:model="dataf" />
            </div>

            <div class="col-span-12">
                <flux:select variant="combobox" label="Transação de Entrada" :filter="false" wire:model="numtransent">
                    <x-slot name="input">
                        <flux:select.input wire:model.live="search_numtransent" placeholder="Pesquisar..." />
                    </x-slot>
                    @foreach ($this->nfEntradas as $nfEntrada)
                        <flux:select.option value="{{ $nfEntrada->numtransent }}">
                            Numtransent: {{ $nfEntrada->numtransent }} - Nota: {{ $nfEntrada->numnota }} - Valor: {{ number_format($nfEntrada->vltotal,2,',','.') }} - Fornecedor: {{ $nfEntrada->fornecedor }} - Emissão: {{ $nfEntrada->dtemissao ? $nfEntrada->dtemissao->format('d/m/Y') : '' }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <div class="mt-4 flex gap-2 col-span-12">
                <flux:button variant="primary" type="submit">Buscar</flux:button>
                <flux:button wire:click="resetar()">Limpar</flux:button>
            </div>
        </form>
    </flux:card>
</div>
