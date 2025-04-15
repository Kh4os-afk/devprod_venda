<div class="space-y-6">
    <flux:card>
        <flux:table id="table" class="display compact" style="width:100%">
            <flux:table.columns>
                <flux:table.column>Código</flux:table.column>
                <flux:table.column>Descrição</flux:table.column>
                <flux:table.column>Cod Auxiliar</flux:table.column>
                <flux:table.column>Embalagem</flux:table.column>
                <flux:table.column>Unidade</flux:table.column>
                <flux:table.column>Qtde</flux:table.column>
                <flux:table.column>P Medio</flux:table.column>
                <flux:table.column>Vl Venda</flux:table.column>
                <flux:table.column>% Venda</flux:table.column>
                <flux:table.column>Vl CMV</flux:table.column>
                <flux:table.column>% Lucro</flux:table.column>
                <flux:table.column>Peso Total</flux:table.column>
                <flux:table.column>Descrição Embalagem</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($resultados as $grupo)
                    @php
                        $total_venda = collect($grupo)->sum(fn($item) => $item->vlvenda);
                        $total_custo = collect($grupo)->sum(fn($item) => $item->vlcustofin);
                    @endphp

                    @foreach ($grupo as $item)
                        @php
                            $perc_venda = $total_venda > 0 ? ($item->vlvenda / $total_venda) * 100 : 0;
                            $perc_lucro = $item->vlvenda > 0 ? (($item->vlvenda - $item->vlcustofin) / $item->vlvenda) * 100 : 0;
                        @endphp
                        <flux:table.row>
                            <flux:table.cell>{{ $item->codprod }}</flux:table.cell>
                            <flux:table.cell>{{ $item->descricao }}</flux:table.cell>
                            <flux:table.cell>{{ $item->codauxiliar }}</flux:table.cell>
                            <flux:table.cell>{{ $item->embalagem }}</flux:table.cell>
                            <flux:table.cell>{{ $item->unidade }}</flux:table.cell>
                            <flux:table.cell>{{ number_format($item->qt, 2, ',', '.') }}</flux:table.cell>
                            <flux:table.cell>{{ number_format($item->vlvenda / $item->qt, 2, ',', '.') }}</flux:table.cell>
                            <flux:table.cell>R$ {{ number_format($item->vlvenda, 2, ',', '.') }}</flux:table.cell>
                            <flux:table.cell>{{ number_format($perc_venda, 2, ',', '.') }}%</flux:table.cell>
                            <flux:table.cell>R$ {{ number_format($item->vlcustofin, 2, ',', '.') }}</flux:table.cell>
                            <flux:table.cell>{{ number_format($perc_lucro, 2, ',', '.') }}%</flux:table.cell>
                            <flux:table.cell>{{ number_format($item->totpeso, 2, ',', '.') }}</flux:table.cell>
                            <flux:table.cell>{{ $item->descricao_embalagem ?? '-' }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>

@assets
<script src="{{ asset('datatables/datatables.min.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>

<link href="{{ asset('datatables/datatables.min.css') }}" rel="stylesheet">
@endassets

@script
<script>
    $(document).ready(function () {
        const table = $('#table').DataTable({
            language: {
                url: '{!! asset('datatables/pt-BR.json') !!}'
            },
            lengthMenu: [
                [15, 25, 50, 100, -1],
                [15, 25, 50, 100, 'All']
            ],
            layout: {
                topStart: 'buttons',
                bottomStart: 'pageLength',
            },
            colReorder: true,
            scrollX: true,
            buttons: [
                'colvis',
                'searchBuilder',
                {
                    extend: 'excelHtml5',
                    title: {!! json_encode($titulo ?? 'Relatório') !!},
                    filename: {!! json_encode($titulo ?? 'Relatório') !!},
                    createEmptyCells: true,
                    customizeData: function (data) {
                        for (var i = 0; i < data.body.length; i++) {
                            for (var j = 0; j < data.body[i].length; j++) {
                                // Verifica se no DataTables o campo é maior que 16 dígitos e é um número para converter em string
                                if (!isNaN(data.body[i][j]) && data.body[i][j].length > 16) {
                                    data.body[i][j] = '\u200C' + data.body[i][j];
                                }

                                // Verifica se o valor numérico começa com '.' e adiciona o zero
                                if (typeof data.body[i][j] === 'string' && data.body[i][j].trim().startsWith('.')) {
                                    data.body[i][j] = '0' + data.body[i][j];
                                }
                            }
                        }
                    },
                    customize: function (xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        // Estilos para a primeira linha: centralizado, negrito, com bordas
                        sheet.querySelectorAll('row').forEach((row, rowIndex) => {
                            if (rowIndex === 0) { // Primeira linha
                                row.querySelectorAll('c').forEach((el) => {
                                    el.setAttribute('s', '51'); // Estilo 51: centralizado e negrito com bordas
                                });
                            } else if (rowIndex === 1) { // Segunda linha
                                row.querySelectorAll('c').forEach((el) => {
                                    el.setAttribute('s', '27'); // Estilo 27: negrito com bordas
                                });
                            } else if (rowIndex >= 2) { // Terceira linha em diante
                                row.querySelectorAll('c').forEach((el) => {
                                    el.setAttribute('s', '25'); // Estilo 25: bordas
                                });
                            }
                        });
                    },
                    exportOptions: {
                        columns: ':visible',
                    }
                },
                {
                    extend: 'print',
                    title: {!! json_encode($titulo ?? 'Relatório') !!}, // Customiza o título
                    messageTop: function () {
                        // Centraliza a data no topo do documento de impressão
                        return '<h2 style="text-align: center; margin-bottom: 30px;">' + {!! json_encode($data ?? '') !!} + '</h2>';
                    },
                    customize: function (win) {
                        $(win.document.body).find('h1').css('text-align', 'center');
                    },
                    exportOptions: {
                        columns: ':visible',
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: {!! json_encode($titulo ?? 'Relatório') !!},
                    filename: {!! json_encode($titulo ?? 'Relatório') !!},
                    orientation: 'landscape',
                    pageSize: 'A3',
                    customize: function (doc) {
                        // Encontra o título e ajusta sua margem inferior
                        doc.content[0].margin = [0, 0, 0, 5]; // Ajuste a margem inferior do título

                        // Centraliza a mensagem no topo do PDF
                        doc.content.splice(1, 0, {
                            margin: [0, 0, 0, 12],
                            alignment: 'center',
                            fontSize: 12, // Aumente esse valor para uma fonte maior
                            text: {!! json_encode($data ?? '') !!},
                        });
                    },
                    exportOptions: {
                        columns: ':visible' // Somente colunas visíveis serão exportadas
                    },
                },
                {
                    extend: 'csvHtml5',
                    title: {!! json_encode($titulo ?? 'Relatório') !!},
                    filename: {!! json_encode($titulo ?? 'Relatório') !!},
                    exportOptions: {
                        columns: ':visible',
                    }
                }
            ],
        });
    });
</script>

@endscript
