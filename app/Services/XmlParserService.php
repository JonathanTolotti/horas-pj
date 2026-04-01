<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class XmlParserService
{
    /**
     * Parse a NF XML file and extract relevant fields.
     * Returns an array with extracted data and parse status.
     * Never throws — on failure, returns xml_parsed=false with error message.
     */
    public function parse(string $storagePath): array
    {
        $result = [
            'invoice_number'  => null,
            'amount'          => null,
            'issued_at'       => null,
            'provider_cnpj'   => null,
            'recipient_cnpj'  => null,
            'provider_name'   => null,
            'recipient_name'  => null,
            'xml_parsed'      => false,
            'parse_error'     => null,
        ];

        try {
            $contents = Storage::get($storagePath);

            if ($contents === null) {
                $result['parse_error'] = 'Arquivo não encontrado no storage.';
                return $result;
            }

            $xml = @simplexml_load_string($contents, 'SimpleXMLElement', LIBXML_NOERROR | LIBXML_NOWARNING);

            if ($xml === false) {
                $result['parse_error'] = 'XML inválido ou corrompido.';
                return $result;
            }

            // Try NF-e (federal product invoice)
            $parsed = $this->parseNFe($xml);

            // Try NFS-e Nacional (SPED/federal service invoice)
            if (!$parsed['found']) {
                $parsed = $this->parseNFSeNacional($xml);
            }

            // Try NFS-e municipal (ABRASF)
            if (!$parsed['found']) {
                $parsed = $this->parseNFSe($xml);
            }

            if (!$parsed['found']) {
                $result['parse_error'] = 'Formato de NF não reconhecido. Os dados precisam ser preenchidos manualmente.';
                return $result;
            }

            $result = array_merge($result, $parsed['data']);
            $result['xml_parsed'] = true;
            $result['parse_error'] = null;

        } catch (\Throwable $e) {
            $result['parse_error'] = 'Erro ao processar o XML: ' . $e->getMessage();
        }

        return $result;
    }

    private function parseNFe(\SimpleXMLElement $xml): array
    {
        // Register NF-e namespace
        $namespaces = $xml->getNamespaces(true);
        $nfeNs = $namespaces[''] ?? $namespaces['nfe'] ?? 'http://www.portalfiscal.inf.br/nfe';

        $xml->registerXPathNamespace('nfe', $nfeNs);

        // Try to find infNFe node
        $infNFe = $xml->xpath('//nfe:infNFe') ?: $xml->xpath('//*[local-name()="infNFe"]');

        if (empty($infNFe)) {
            return ['found' => false, 'data' => []];
        }

        $info = $infNFe[0];
        $info->registerXPathNamespace('nfe', $nfeNs);

        $data = [];

        // Invoice number (nNF)
        $nNF = $info->xpath('.//nfe:ide/nfe:nNF') ?: $info->xpath('.//*[local-name()="nNF"]');
        if (!empty($nNF)) {
            $data['invoice_number'] = (string) $nNF[0];
        }

        // Issue date (dhEmi or dEmi)
        $dhEmi = $info->xpath('.//nfe:ide/nfe:dhEmi') ?: $info->xpath('.//*[local-name()="dhEmi"]');
        if (!empty($dhEmi)) {
            $data['issued_at'] = substr((string) $dhEmi[0], 0, 10);
        } else {
            $dEmi = $info->xpath('.//nfe:ide/nfe:dEmi') ?: $info->xpath('.//*[local-name()="dEmi"]');
            if (!empty($dEmi)) {
                $data['issued_at'] = (string) $dEmi[0];
            }
        }

        // Total value (vNF)
        $vNF = $info->xpath('.//nfe:total/nfe:ICMSTot/nfe:vNF') ?: $info->xpath('.//*[local-name()="vNF"]');
        if (!empty($vNF)) {
            $data['amount'] = (float) $vNF[0];
        }

        // Provider (emitente)
        $emitCNPJ = $info->xpath('.//nfe:emit/nfe:CNPJ') ?: $info->xpath('.//*[local-name()="emit"]/*[local-name()="CNPJ"]');
        if (!empty($emitCNPJ)) {
            $data['provider_cnpj'] = $this->formatCnpj((string) $emitCNPJ[0]);
        }

        $emitName = $info->xpath('.//nfe:emit/nfe:xNome') ?: $info->xpath('.//*[local-name()="emit"]/*[local-name()="xNome"]');
        if (!empty($emitName)) {
            $data['provider_name'] = (string) $emitName[0];
        }

        // Recipient (destinatário)
        $destCNPJ = $info->xpath('.//nfe:dest/nfe:CNPJ') ?: $info->xpath('.//*[local-name()="dest"]/*[local-name()="CNPJ"]');
        if (!empty($destCNPJ)) {
            $data['recipient_cnpj'] = $this->formatCnpj((string) $destCNPJ[0]);
        }

        $destName = $info->xpath('.//nfe:dest/nfe:xNome') ?: $info->xpath('.//*[local-name()="dest"]/*[local-name()="xNome"]');
        if (!empty($destName)) {
            $data['recipient_name'] = (string) $destName[0];
        }

        return ['found' => !empty($data), 'data' => $data];
    }

    private function parseNFSeNacional(\SimpleXMLElement $xml): array
    {
        // NFS-e Nacional (SPED) — xmlns="http://www.sped.fazenda.gov.br/nfse"
        // Identificador: presença de <infNFSe> com tags <emit>, <toma>, <nNFSe>, <vLiq>

        $infNFSe = @$xml->xpath('//*[local-name()="infNFSe"]');
        if (empty($infNFSe)) {
            return ['found' => false, 'data' => []];
        }

        $data = [];

        // Número da NFS-e
        $nNFSe = @$xml->xpath('//*[local-name()="nNFSe"]');
        if (!empty($nNFSe)) {
            $data['invoice_number'] = (string) $nNFSe[0];
        }

        // Data de emissão — prefere dhEmi dentro de infDPS, fallback dhProc
        $dhEmi = @$xml->xpath('//*[local-name()="infDPS"]//*[local-name()="dhEmi"]')
               ?: @$xml->xpath('//*[local-name()="dhEmi"]');
        if (!empty($dhEmi)) {
            $data['issued_at'] = substr((string) $dhEmi[0], 0, 10);
        } else {
            $dhProc = @$xml->xpath('//*[local-name()="dhProc"]');
            if (!empty($dhProc)) {
                $data['issued_at'] = substr((string) $dhProc[0], 0, 10);
            }
        }

        // Valor — prefere vLiq no bloco valores do infNFSe, fallback vServ
        $vLiq = @$xml->xpath('//*[local-name()="infNFSe"]/*[local-name()="valores"]/*[local-name()="vLiq"]')
               ?: @$xml->xpath('//*[local-name()="vLiq"]');
        if (!empty($vLiq)) {
            $data['amount'] = (float) $vLiq[0];
        } else {
            $vServ = @$xml->xpath('//*[local-name()="vServ"]');
            if (!empty($vServ)) {
                $data['amount'] = (float) $vServ[0];
            }
        }

        // Prestador (emit)
        $emitCNPJ = @$xml->xpath('//*[local-name()="emit"]/*[local-name()="CNPJ"]');
        if (!empty($emitCNPJ)) {
            $data['provider_cnpj'] = $this->formatCnpj((string) $emitCNPJ[0]);
        }

        $emitName = @$xml->xpath('//*[local-name()="emit"]/*[local-name()="xNome"]');
        if (!empty($emitName)) {
            $data['provider_name'] = (string) $emitName[0];
        }

        // Tomador (toma)
        $tomaCNPJ = @$xml->xpath('//*[local-name()="toma"]/*[local-name()="CNPJ"]');
        if (!empty($tomaCNPJ)) {
            $data['recipient_cnpj'] = $this->formatCnpj((string) $tomaCNPJ[0]);
        }

        $tomaName = @$xml->xpath('//*[local-name()="toma"]/*[local-name()="xNome"]');
        if (!empty($tomaName)) {
            $data['recipient_name'] = (string) $tomaName[0];
        }

        return ['found' => !empty($data), 'data' => $data];
    }

    private function parseNFSe(\SimpleXMLElement $xml): array
    {
        // NFS-e: multiple municipal formats, try common paths
        $data = [];

        // Invoice number
        foreach (['//Numero', '//*[local-name()="Numero"]', '//*[local-name()="NumeroNfse"]'] as $xpath) {
            $nodes = @$xml->xpath($xpath);
            if (!empty($nodes)) {
                $data['invoice_number'] = (string) $nodes[0];
                break;
            }
        }

        // Issue date
        foreach (['//*[local-name()="DataEmissao"]', '//*[local-name()="Competencia"]'] as $xpath) {
            $nodes = @$xml->xpath($xpath);
            if (!empty($nodes)) {
                $data['issued_at'] = substr((string) $nodes[0], 0, 10);
                break;
            }
        }

        // Amount (ValorServicos or ValorLiquidoNfse)
        foreach (['//*[local-name()="ValorLiquidoNfse"]', '//*[local-name()="ValorServicos"]'] as $xpath) {
            $nodes = @$xml->xpath($xpath);
            if (!empty($nodes)) {
                $data['amount'] = (float) $nodes[0];
                break;
            }
        }

        // Provider CNPJ (Prestador)
        $providerCnpj = @$xml->xpath('//*[local-name()="Prestador"]//*[local-name()="Cnpj"]')
            ?: @$xml->xpath('//*[local-name()="CpfCnpj"]//*[local-name()="Cnpj"]');
        if (!empty($providerCnpj)) {
            $data['provider_cnpj'] = $this->formatCnpj((string) $providerCnpj[0]);
        }

        $providerName = @$xml->xpath('//*[local-name()="Prestador"]//*[local-name()="RazaoSocial"]')
            ?: @$xml->xpath('//*[local-name()="RazaoSocial"]');
        if (!empty($providerName)) {
            $data['provider_name'] = (string) $providerName[0];
        }

        // Recipient CNPJ (Tomador)
        $recipientCnpj = @$xml->xpath('//*[local-name()="Tomador"]//*[local-name()="Cnpj"]');
        if (!empty($recipientCnpj)) {
            $data['recipient_cnpj'] = $this->formatCnpj((string) $recipientCnpj[0]);
        }

        $recipientName = @$xml->xpath('//*[local-name()="Tomador"]//*[local-name()="RazaoSocial"]');
        if (!empty($recipientName)) {
            $data['recipient_name'] = (string) $recipientName[0];
        }

        return ['found' => !empty($data), 'data' => $data];
    }

    private function formatCnpj(string $cnpj): string
    {
        $digits = preg_replace('/\D/', '', $cnpj);

        if (strlen($digits) === 14) {
            return substr($digits, 0, 2) . '.' .
                   substr($digits, 2, 3) . '.' .
                   substr($digits, 5, 3) . '/' .
                   substr($digits, 8, 4) . '-' .
                   substr($digits, 12, 2);
        }

        return $cnpj;
    }
}
