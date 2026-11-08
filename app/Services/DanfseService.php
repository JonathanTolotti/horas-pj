<?php

namespace App\Services;

use App\Models\InvoiceXml;
use Danfse\Danfse\Danfse;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use SimpleXMLElement;

/**
 * Gera o PDF do DANFSe a partir do XML da NFS-e Nacional (padrão SPED),
 * convertendo as tags do XML para o payload esperado pelo nfse-nacional/danfse-php.
 */
class DanfseService
{
    /** Domínios do leiaute da NFS-e Nacional: o DANFSe exibe a descrição, não o código */
    private const LABELS = [
        'opSimpNac' => [
            '1' => 'Não Optante',
            '2' => 'Optante - Microempreendedor Individual (MEI)',
            '3' => 'Optante - Microempresa ou Empresa de Pequeno Porte (ME/EPP)',
        ],
        'regApTribSN' => [
            '1' => 'Regime de apuração dos tributos federais e municipal pelo SN',
            '2' => 'Regime de apuração dos tributos federais pelo SN e ISSQN pela NFS-e',
            '3' => 'Regime de apuração dos tributos federais e municipal pela NFS-e',
        ],
        'regEspTrib' => [
            '0' => 'Nenhum',
            '1' => 'Ato Cooperado (Cooperativa)',
            '2' => 'Estimativa',
            '3' => 'Microempresa Municipal',
            '4' => 'Notário ou Registrador',
            '5' => 'Profissional Autônomo',
            '6' => 'Sociedade de Profissionais',
            '9' => 'Outros',
        ],
        'tribISSQN' => [
            '1' => 'Operação Tributável',
            '2' => 'Imunidade',
            '3' => 'Exportação de Serviço',
            '4' => 'Não Incidência',
        ],
        'tpRetISSQN' => [
            '1' => 'Não Retido',
            '2' => 'Retido pelo Tomador',
            '3' => 'Retido pelo Intermediário',
        ],
        'tpSusp' => [
            '1' => 'Decisão Judicial',
            '2' => 'Processo Administrativo',
        ],
    ];

    public function render(InvoiceXml $xml): string
    {
        $contents = Storage::get($xml->path);

        if ($contents === null) {
            throw new RuntimeException('Arquivo XML não encontrado no storage.');
        }

        $payload = $this->toPayload($contents);

        // O XML só traz o código IBGE do município do tomador; usa a cidade do cadastro da empresa
        if ($xml->company?->cidade && empty($payload['infNfse']['dps']['infDps']['tomador']['endereco']['municipio'])) {
            $payload['infNfse']['dps']['infDps']['tomador']['endereco']['municipio'] = trim(
                $xml->company->cidade . ($xml->company->uf ? ' / ' . $xml->company->uf : '')
            );
        }

        // jasperphp/TCPDF emitem warnings internos que o Laravel converteria em exceção
        $level = error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_DEPRECATED);

        try {
            return (new Danfse)->render($payload);
        } finally {
            error_reporting($level);
        }
    }

    public function toPayload(string $contents): array
    {
        // Bloqueia DTD/entidades externas (XXE) antes de carregar
        if (preg_match('/<!DOCTYPE|<!ENTITY/i', $contents)) {
            throw new RuntimeException('XML com DOCTYPE/entidades não é permitido.');
        }

        $root = @simplexml_load_string($contents, 'SimpleXMLElement', LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_NONET);

        if ($root === false) {
            throw new RuntimeException('XML inválido ou corrompido.');
        }

        $inf = $this->node($root, '//*[local-name()="infNFSe"]');
        $dps = $inf ? $this->node($inf, './/*[local-name()="infDPS"]') : null;

        if (!$inf || !$dps) {
            throw new RuntimeException('O DANFSe só pode ser gerado para NFS-e do padrão nacional.');
        }

        $emit = $this->node($inf, './*[local-name()="emit"]');
        $prest = $this->node($dps, './*[local-name()="prest"]');
        $toma = $this->node($dps, './*[local-name()="toma"]');
        $serv = $this->node($dps, './*[local-name()="serv"]');
        $valoresDps = $this->node($dps, './*[local-name()="valores"]');
        $tribMun = $this->node($valoresDps, './*[local-name()="trib"]/*[local-name()="tribMun"]');
        $tribFed = $this->node($valoresDps, './*[local-name()="trib"]/*[local-name()="tribFed"]');
        $totTrib = $this->node($valoresDps, './*[local-name()="trib"]/*[local-name()="totTrib"]/*[local-name()="vTotTrib"]');

        return [
            'infNfse' => [
                'id'                  => (string) ($inf['Id'] ?? ''),
                'numeroNfse'          => $this->text($inf, 'nNFSe'),
                'localEmissao'        => $this->text($inf, 'xLocEmi'),
                'localPrestacao'      => $this->text($inf, 'xLocPrestacao'),
                'nomeLocalIncidencia' => $this->text($inf, 'xLocIncid'),
                'descricaoNbs'        => $this->text($inf, 'xNBS'),
                'dataProcessamento'   => $this->text($inf, 'dhProc'),
                'outrasInformacoes'   => $this->text($inf, 'xOutInf'),
                'emitente'            => [
                    'cnpj'               => $this->text($emit, 'CNPJ'),
                    'cpf'                => $this->text($emit, 'CPF'),
                    'nome'               => $this->text($emit, 'xNome'),
                    'inscricaoMunicipal' => $this->text($emit, 'IM'),
                    'telefone'           => $this->phone($this->text($emit, 'fone')),
                    'email'              => $this->text($emit, 'email'),
                    'endereco'           => $this->address($this->node($emit, './*[local-name()="enderNac"]')),
                ],
                'valores'             => [
                    'valorCalculadoDeducaoReducao'     => $this->text($inf, 'valores/vCalcDR'),
                    'tipoBeneficioMunicipal'           => $this->text($inf, 'valores/tpBM'),
                    'valorCalculadoBeneficioMunicipal' => $this->text($inf, 'valores/vCalcBM'),
                    'baseCalculo'                      => $this->text($inf, 'valores/vBC'),
                    'aliquotaAplicada'                 => $this->text($inf, 'valores/pAliqAplic'),
                    'valorIssqn'                       => $this->text($inf, 'valores/vISSQN'),
                    'valorTotalRetido'                 => $this->text($inf, 'valores/vTotalRet'),
                    'valorLiquido'                     => $this->text($inf, 'valores/vLiq'),
                ],
                'dps'                 => [
                    'infDps' => [
                        'dataEmissao'        => $this->text($dps, 'dhEmi'),
                        'dataCompetencia'    => $this->text($dps, 'dCompet'),
                        'serie'              => $this->text($dps, 'serie'),
                        'numeroDps'          => $this->text($dps, 'nDPS'),
                        'codigoLocalEmissao' => $this->text($dps, 'cLocEmi'),
                        'prestador'          => [
                            'nome'               => $this->text($prest, 'xNome'),
                            'inscricaoMunicipal' => $this->text($prest, 'IM'),
                            'telefone'           => $this->phone($this->text($prest, 'fone')),
                            'email'              => $this->text($prest, 'email'),
                            'regimeTributario'   => [
                                'opcaoSimplesNacional'     => $this->label('opSimpNac', $this->text($prest, 'regTrib/opSimpNac')),
                                'regimeApuracaoTributosSn' => $this->label('regApTribSN', $this->text($prest, 'regTrib/regApTribSN')),
                                'regimeEspecialTributacao' => $this->label('regEspTrib', $this->text($prest, 'regTrib/regEspTrib')),
                            ],
                        ],
                        'tomador'            => [
                            'cnpj'               => $this->text($toma, 'CNPJ'),
                            'cpf'                => $this->text($toma, 'CPF'),
                            'nome'               => $this->text($toma, 'xNome'),
                            'inscricaoMunicipal' => $this->text($toma, 'IM'),
                            'telefone'           => $this->phone($this->text($toma, 'fone')),
                            'email'              => $this->text($toma, 'email'),
                            'endereco'           => $this->address($this->node($toma, './*[local-name()="end"]')),
                        ],
                        'servico'            => [
                            'codigoServico'         => [
                                'codigoTributacaoNacional'  => $this->text($serv, 'cServ/cTribNac'),
                                'codigoTributacaoMunicipal' => $this->text($serv, 'cServ/cTribMun'),
                                'descricaoServico'          => $this->text($serv, 'cServ/xDescServ'),
                                'codigoNbs'                 => $this->text($serv, 'cServ/cNBS'),
                            ],
                            'localPrestacao'        => [
                                'codigoPaisPrestacao' => $this->text($serv, 'locPrest/cPaisPrestacao'),
                            ],
                            'informacaoComplemento' => [
                                'informacoesComplementares' => $this->text($serv, 'infoCompl/xInfComp'),
                            ],
                        ],
                        'valores'            => [
                            'valorServicoPrestado' => [
                                'valorServico' => $this->text($valoresDps, 'vServPrest/vServ'),
                            ],
                            'desconto'             => [
                                'valorDescontoIncondicionado' => $this->text($valoresDps, 'vDescCondIncond/vDescIncond'),
                                'valorDescontoCondicionado'   => $this->text($valoresDps, 'vDescCondIncond/vDescCond'),
                            ],
                            'deducaoReducao'       => [
                                'valorDeducaoReducao' => $this->text($valoresDps, 'vDedRed/vDR'),
                            ],
                            'tributacao'           => [
                                'tributacaoIssqn'                       => $this->label('tribISSQN', $this->text($tribMun, 'tribISSQN')),
                                'tipoImunidade'                         => $this->text($tribMun, 'tpImunidade'),
                                'tipoSuspensao'                         => $this->label('tpSusp', $this->text($tribMun, 'exigSusp/tpSusp')),
                                'numeroProcessoSuspensao'               => $this->text($tribMun, 'exigSusp/nProcesso'),
                                'tipoRetencaoIssqn'                     => $this->label('tpRetISSQN', $this->text($tribMun, 'tpRetISSQN')),
                                'aliquota'                              => $this->text($tribMun, 'pAliq'),
                                'valorPis'                              => $this->text($tribFed, 'piscofins/vPis'),
                                'valorCofins'                           => $this->text($tribFed, 'piscofins/vCofins'),
                                'tipoRetencaoPisCofins'                 => $this->text($tribFed, 'piscofins/tpRetPisCofins'),
                                'valorRetidoContribuicaoPrevidenciaria' => $this->text($tribFed, 'vRetCP'),
                                'valorRetidoIrrf'                       => $this->text($tribFed, 'vRetIRRF'),
                                'valorRetidoCsll'                       => $this->text($tribFed, 'vRetCSLL'),
                                'valorTotalTributosFederais'            => $this->text($totTrib, 'vTotTribFed'),
                                'valorTotalTributosEstaduais'           => $this->text($totTrib, 'vTotTribEst'),
                                'valorTotalTributosMunicipais'          => $this->text($totTrib, 'vTotTribMun'),
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Endereço nacional: <enderNac> do emitente tem tudo no mesmo nível;
     * <end> do tomador separa cMun/CEP em <endNac>.
     */
    private function address(?SimpleXMLElement $node): array
    {
        return [
            'logradouro'      => $this->text($node, 'xLgr'),
            'numero'          => $this->text($node, 'nro'),
            'complemento'     => $this->text($node, 'xCpl'),
            'bairro'          => $this->text($node, 'xBairro'),
            'codigoMunicipio' => $this->text($node, 'cMun') ?? $this->text($node, 'endNac/cMun'),
            'uf'              => $this->text($node, 'UF'),
            'cep'             => $this->cep($this->text($node, 'CEP') ?? $this->text($node, 'endNac/CEP')),
        ];
    }

    private function node(?SimpleXMLElement $context, string $xpath): ?SimpleXMLElement
    {
        $nodes = $context ? @$context->xpath($xpath) : null;

        return $nodes ? $nodes[0] : null;
    }

    /**
     * Lê o texto de um caminho relativo (ex.: "cServ/cTribNac") ignorando namespaces.
     * Retorna null quando a tag não existe — o mapper da lib trata null como vazio.
     */
    private function text(?SimpleXMLElement $context, string $path): ?string
    {
        $xpath = './' . implode('/', array_map(
            fn (string $tag) => '*[local-name()="' . $tag . '"]',
            explode('/', $path),
        ));

        $node = $this->node($context, $xpath);

        if ($node === null) {
            return null;
        }

        $value = trim((string) $node);

        return $value === '' ? null : $value;
    }

    private function label(string $field, ?string $code): ?string
    {
        return $code === null ? null : (self::LABELS[$field][$code] ?? $code);
    }

    private function cep(?string $cep): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $cep);

        return strlen($digits) === 8 ? substr($digits, 0, 5) . '-' . substr($digits, 5) : $cep;
    }

    private function phone(?string $phone): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $phone);

        return match (strlen($digits)) {
            10 => sprintf('(%s) %s-%s', substr($digits, 0, 2), substr($digits, 2, 4), substr($digits, 6)),
            11 => sprintf('(%s) %s-%s', substr($digits, 0, 2), substr($digits, 2, 5), substr($digits, 7)),
            default => $phone,
        };
    }
}
