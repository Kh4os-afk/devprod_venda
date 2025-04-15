<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class VendasShow extends Component
{
    public array $resultados = [];
    public $numtransent;
    public $titulo;

    public function mount()
    {
        $this->numtransent = request()->query('numtransent');
        $this->titulo = 'Venda Nota ' . $this->numtransent ?? '';
        $datai = request()->query('datai');
        $dataf = request()->query('dataf');
        $codprods = explode(',', request()->query('pcmov'));

        foreach ($codprods as $codprod) {
            $dados = DB::connection('oracle')->select("
                SELECT CODPROD,
                       DESCRICAO,
                       CODAUXILIAR,
                       EMBALAGEM,
                       UNIDADE,
                       SUM (QT) QT,
                       SUM (VLVENDA) VLVENDA,
                       SUM (VLCUSTOFIN) VLCUSTOFIN,
                       SUM (TOTPESO) TOTPESO,
                       DESCRICAOECF DESCRICAO_EMBALAGEM,
                       SUM (PERCENTUAL_PESO_QT) PERCENTUAL_PESO_QT,
                       SUM (PERCENTUAL_PESO) PERCENTUAL_PESO
                  FROM (SELECT PCPEDI.CODPROD,
                               PCPRODUT.DESCRICAO,
                               PCPRODUT.CODAUXILIAR,
                               PCPRODUT.EMBALAGEM,
                               PCPRODUT.UNIDADE,
                               TRUNC (SUM (PCPEDI.QT)) AS QT,
                                 SUM (
                                     CASE
                                         WHEN NVL (PCPEDI.BONIFIC, 'N') = 'N'
                                         THEN
                                             DECODE (
                                                 PCPEDC.CONDVENDA,
                                                 5, 0,
                                                 6, 0,
                                                 11, 0,
                                                 12, 0,
                                                 NVL (
                                                     PCPEDI.VLSUBTOTITEM,
                                                     (DECODE (
                                                          NVL (PCPEDI.TRUNCARITEM, 'N'),
                                                          'N', ROUND (
                                                                     (NVL (PCPEDI.QT, 0))
                                                                   * (  NVL (PCPEDI.PVENDA,
                                                                             0)
                                                                      + NVL (
                                                                            PCPEDI.VLOUTRASDESP,
                                                                            0)
                                                                      + NVL (PCPEDI.VLFRETE,
                                                                             0)),
                                                                   2),
                                                          TRUNC (
                                                                (NVL (PCPEDI.QT, 0))
                                                              * (  NVL (PCPEDI.PVENDA, 0)
                                                                 + NVL (PCPEDI.VLOUTRASDESP,
                                                                        0)
                                                                 + NVL (PCPEDI.VLFRETE, 0)),
                                                              2)))))
                                         ELSE
                                             0
                                     END)
                               - SUM (
                                     CASE
                                         WHEN NVL (PCPEDI.BONIFIC, 'N') = 'N'
                                         THEN
                                             DECODE (PCPEDC.CONDVENDA,
                                                     5, 0,
                                                     6, 0,
                                                     11, 0,
                                                     12, 0,
                                                     NVL (PCPEDI.QT, 0) * (0 + 0))
                                         ELSE
                                             0
                                     END)
                                   VLVENDA,
                               SUM (NVL (PCPEDI.QT, 0) * NVL (PCPEDI.VLCUSTOFIN, 0))
                                   VLCUSTOFIN,
                               0 TOTPESO,
                               '' DESCRICAOECF,
                               0 PERCENTUAL_PESO_QT,
                               0 PERCENTUAL_PESO
                          FROM PCPEDI,
                               PCPEDC,
                               PCUSUARI,
                               PCPRODUT,
                               PCDEPTO,
                               PCCLIENT,
                               PCPRACA,
                               PCSUPERV
                         WHERE     PCPEDI.NUMPED = PCPEDC.NUMPED
                               AND PCPEDC.CODUSUR = PCUSUARI.CODUSUR
                               AND PCPEDC.CODCLI = PCCLIENT.CODCLI
                               AND PCPEDC.CODPRACA = PCPRACA.CODPRACA
                               AND PCPEDI.CODPROD = PCPRODUT.CODPROD
                               AND PCPRODUT.CODEPTO = PCDEPTO.CODEPTO
                               AND PCPEDC.CODSUPERVISOR = PCSUPERV.CODSUPERVISOR
                               AND PCPEDI.CODAUXILIAR NOT IN
                                       (SELECT CODAUXILIAR
                                          FROM PCEMBALAGEM
                                         WHERE     PCPEDI.CODPROD = PCEMBALAGEM.CODPROD
                                               AND PCPEDI.CODAUXILIAR =
                                                       PCEMBALAGEM.CODAUXILIAR
                                               AND PCPEDC.CODFILIAL = PCEMBALAGEM.CODFILIAL)
                               AND PCPEDC.DTCANCEL IS NULL
                               AND (PCPEDC.DATA >= '$datai' AND PCPEDC.DATA <= '$dataf')
                               AND (PCPEDI.DATA >= '$datai' AND PCPEDI.DATA <= '$dataf')
                               AND PCPEDI.CODPROD = $codprod
                               AND PCPEDC.CODFILIAL <> '99'
                               AND PCPEDC.CONDVENDA NOT IN (4,
                                                            5,
                                                            6,
                                                            8,
                                                            10,
                                                            11,
                                                            12,
                                                            13,
                                                            16,
                                                            20)
                        GROUP BY PCPEDI.CODPROD,
                                 PCPRODUT.DESCRICAO,
                                 PCPRODUT.CODAUXILIAR,
                                 PCPRODUT.EMBALAGEM,
                                 PCPRODUT.UNIDADE
                        UNION ALL
                        SELECT PCPEDI.CODPROD,
                               PCPRODUT.DESCRICAO,
                               PCEMBALAGEM.CODAUXILIAR,
                               PCEMBALAGEM.EMBALAGEM,
                               PCEMBALAGEM.UNIDADE,
                               TRUNC (SUM (PCPEDI.QT)) AS QT,
                                 SUM (
                                     CASE
                                         WHEN NVL (PCPEDI.BONIFIC, 'N') = 'N'
                                         THEN
                                             DECODE (
                                                 PCPEDC.CONDVENDA,
                                                 5, 0,
                                                 6, 0,
                                                 11, 0,
                                                 12, 0,
                                                 NVL (
                                                     PCPEDI.VLSUBTOTITEM,
                                                     (DECODE (
                                                          NVL (PCPEDI.TRUNCARITEM, 'N'),
                                                          'N', ROUND (
                                                                     (NVL (PCPEDI.QT, 0))
                                                                   * (  NVL (PCPEDI.PVENDA,
                                                                             0)
                                                                      + NVL (
                                                                            PCPEDI.VLOUTRASDESP,
                                                                            0)
                                                                      + NVL (PCPEDI.VLFRETE,
                                                                             0)),
                                                                   2),
                                                          TRUNC (
                                                                (NVL (PCPEDI.QT, 0))
                                                              * (  NVL (PCPEDI.PVENDA, 0)
                                                                 + NVL (PCPEDI.VLOUTRASDESP,
                                                                        0)
                                                                 + NVL (PCPEDI.VLFRETE, 0)),
                                                              2)))))
                                         ELSE
                                             0
                                     END)
                               - SUM (
                                     CASE
                                         WHEN NVL (PCPEDI.BONIFIC, 'N') = 'N'
                                         THEN
                                             DECODE (PCPEDC.CONDVENDA,
                                                     5, 0,
                                                     6, 0,
                                                     11, 0,
                                                     12, 0,
                                                     NVL (PCPEDI.QT, 0) * (0 + 0))
                                         ELSE
                                             0
                                     END)
                                   VLVENDA,
                               SUM (NVL (PCPEDI.QT, 0) * NVL (PCPEDI.VLCUSTOFIN, 0))
                                   VLCUSTOFIN,
                               SUM (NVL (PCEMBALAGEM.PESOLIQ, 0) * NVL (PCPEDI.QT, 0))
                                   TOTPESO,
                               PCEMBALAGEM.DESCRICAOECF,
                               SUM (
                                   (NVL (PCEMBALAGEM.PESOLIQ, 0) * NVL (PCPEDI.QT, 1)) / 100)
                                   PERCENTUAL_PESO_QT,
                               (  (SUM (NVL (PCEMBALAGEM.PESOLIQ, 0) * NVL (PCPEDI.QT, 1)))
                                / NVL (
                                      (SELECT SUM (
                                                    NVL (
                                                        DECODE (PCEMBALAGEM.PESOLIQ,
                                                                0, 1,
                                                                PCEMBALAGEM.PESOLIQ),
                                                        1)
                                                  * NVL (PCPEDI.QT, 1))
                                         FROM PCPEDI,
                                              PCPEDC,
                                              PCUSUARI,
                                              PCPRODUT,
                                              PCDEPTO,
                                              PCCLIENT,
                                              PCPRACA,
                                              PCEMBALAGEM,
                                              PCSUPERV
                                        WHERE     PCPEDI.NUMPED = PCPEDC.NUMPED
                                              AND PCPEDC.CODUSUR = PCUSUARI.CODUSUR
                                              AND PCPEDI.CODPROD = PCPRODUT.CODPROD
                                              AND PCPEDC.CODCLI = PCCLIENT.CODCLI
                                              AND PCPEDC.CODPRACA = PCPRACA.CODPRACA
                                              AND PCPRODUT.CODEPTO = PCDEPTO.CODEPTO
                                              AND PCPEDI.CODPROD = PCEMBALAGEM.CODPROD
                                              AND PCPEDI.CODAUXILIAR =
                                                      PCEMBALAGEM.CODAUXILIAR
                                              AND PCPEDC.CODFILIAL = PCEMBALAGEM.CODFILIAL
                                              AND PCPEDC.CODSUPERVISOR =
                                                      PCSUPERV.CODSUPERVISOR
                                              AND PCPEDC.DTCANCEL IS NULL
                                              AND (    PCPEDC.DATA >= '$datai'
                                                   AND PCPEDC.DATA <= '$dataf')
                                              AND (    PCPEDI.DATA >= '$datai'
                                                   AND PCPEDI.DATA <= '$dataf')
                                              AND PCPEDI.CODPROD = $codprod
                                              AND PCPEDC.CODFILIAL <> '99'                                                  
                                              AND PCPEDC.CONDVENDA NOT IN (4,
                                                                           5,
                                                                           6,
                                                                           8,
                                                                           10,
                                                                           11,
                                                                           12,
                                                                           13,
                                                                           16,
                                                                           20)),
                                      1)
                                * 100)
                                   PERCENTUAL_PESO
                          FROM PCPEDI,
                               PCPEDC,
                               PCUSUARI,
                               PCPRODUT,
                               PCDEPTO,
                               PCCLIENT,
                               PCPRACA,
                               PCEMBALAGEM,
                               PCSUPERV
                         WHERE     PCPEDI.NUMPED = PCPEDC.NUMPED
                               AND PCPEDC.CODUSUR = PCUSUARI.CODUSUR
                               AND PCPEDI.CODPROD = PCPRODUT.CODPROD
                               AND PCPEDC.CODCLI = PCCLIENT.CODCLI
                               AND PCPEDC.CODPRACA = PCPRACA.CODPRACA
                               AND PCPRODUT.CODEPTO = PCDEPTO.CODEPTO
                               AND PCPEDI.CODPROD = PCEMBALAGEM.CODPROD
                               AND PCPEDI.CODAUXILIAR = PCEMBALAGEM.CODAUXILIAR
                               AND PCPEDC.CODFILIAL = PCEMBALAGEM.CODFILIAL
                               AND PCPEDC.CODSUPERVISOR = PCSUPERV.CODSUPERVISOR
                               AND PCPEDC.DTCANCEL IS NULL
                               AND (PCPEDC.DATA >= '$datai' AND PCPEDC.DATA <= '$dataf')
                               AND (PCPEDI.DATA >= '$datai' AND PCPEDI.DATA <= '$dataf')
                               AND PCPEDI.CODPROD = $codprod
                               AND PCPEDC.CODFILIAL <> '99'
                               AND PCPEDC.CONDVENDA NOT IN (4,
                                                            5,
                                                            6,
                                                            8,
                                                            10,
                                                            11,
                                                            12,
                                                            13,
                                                            16,
                                                            20)
                        GROUP BY PCPEDI.CODPROD,
                                 PCPRODUT.DESCRICAO,
                                 PCEMBALAGEM.CODAUXILIAR,
                                 PCEMBALAGEM.EMBALAGEM,
                                 PCEMBALAGEM.UNIDADE,
                                 PCEMBALAGEM.DESCRICAOECF)
                GROUP BY CODPROD,
                         DESCRICAO,
                         CODAUXILIAR,
                         EMBALAGEM,
                         UNIDADE,
                         DESCRICAOECF
                ORDER BY DESCRICAO
            ");

            if (!empty($dados)) {
                $this->resultados[] = $dados;
            }
        }
    }

    public function render()
    {
        return view('livewire.abastecimento-show', [
            'resultados' => $this->resultados
        ])->title('Venda Nota ' . $this->numtransent ?? '');
    }
}
