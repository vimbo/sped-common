<?php

namespace NFePHP\Common;

use DOMDocument;
use DOMNode;

class SanitizeErrors
{
    protected static $line;
    protected static $find;

    /**
     * @param string $message
     * @param string $xml
     * @return mixed|string
     */
    public static function handleMessage(string $message, string $xml)
    {
        $domSearch = self::getFormatedXml($xml);
        self::$find = null;
        //$message = str_replace(['{http://www.portalfiscal.inf.br/nfe}'], '', $message);
        $message = preg_replace('/\{(.*?)\}/s', '', $message);

        if(strpos($message, 'Expected is') !== false){
            $treated = str_replace([
                "Element ",
                "'",
                "Missing child element(s)",
                ". Expected is ",
                "one of ",
                "This element is not expected",
                "(",
                ")",
                '.'
            ], '', $message);

            $treated = explode(': ', $treated);

            if(count($treated) == 2){
                $message = '';
                self::findNodeByLine($domSearch->getElementsByTagName('infNFe')->item(0));
                if(!empty(self::$find['nItem'])){
                    $nItem = self::$find['nItem'];
                    $message .= "Produto/Serviço {$nItem}: ";
                }

                if(self::$find['node'] == 'emit'){
                    $message .= 'Emitente: ';
                }else if(self::$find['node'] == 'dest'){
                    $message .= 'Destinatário: ';
                }else if(self::$find['node'] == 'transp'){
                    $message .= 'Transportador: ';
                }

                if(!empty(self::$find['item'])){
                    $item = self::$find['item'];
                    $message .= "{$item} - ";
                }

                $tag = trim($treated[0]);
                $tagLack = trim($treated[1]);
                $message .= "Erro na tag <b>{$tag}</b>, está faltando a configuração do <b>{$tagLack}</b><br>";
            }
        }

        return $message;
    }

    /**
     * @param DOMNode $domNode
     */
    protected static function findNodeByLine(DOMNode $domNode)
    {
        foreach ($domNode->childNodes as $node)
        {
            if($node->getLineNo() == self::$line){
                $parentNode = self::getParentNode($node);
                $nodeName = $nItem = $item = null;
                if(!empty($parentNode)){
                    $nodeName = $parentNode->nodeName;
                    $nItem = $parentNode->getAttribute('nItem');

                    if($parentNode->nodeName == 'det'){
                        $item = $parentNode->getElementsByTagName('xProd')->item(0);
                        $item = is_object($item) ? $item->nodeValue : null;
                    }else if($parentNode->nodeName == 'emit' || $parentNode->nodeName == 'dest' || $parentNode->nodeName == 'transp'){
                        $item = $parentNode->getElementsByTagName('xNome')->item(0);
                        $item = is_object($item) ? $item->nodeValue : null;
                    }else if($parentNode->nodeName == 'total'){
                        $item = 'Totalizadores';
                    }else if($parentNode->nodeName == 'cobr'){
                        $item = 'Cobrança';
                    }else if($parentNode->nodeName == 'pag'){
                        $item = 'Pagamento';
                    }else if($parentNode->nodeName == 'ide'){
                        $item = 'Identificação';
                    }
                }

                self::$find = ['node' => $nodeName, 'nItem' => $nItem, 'item' => $item];
            }

            if($node->hasChildNodes()) {
                self::findNodeByLine($node);
            }
        }
    }

    /**
     * @param DOMNode $node
     * @return DOMNode|null
     */
    protected static function getParentNode(DOMNode $node)
    {
        if($node->nodeName == 'infNFe') {
            return null;
        }else if($node->nodeName == 'ide'){
            return $node;
        }else if($node->nodeName == 'det'){
            return $node;
        }else if($node->nodeName == 'emit'){
            return $node;
        }else if($node->nodeName == 'dest'){
            return $node;
        }else if($node->nodeName == 'total'){
            return $node;
        }else if($node->nodeName == 'transp'){
            return $node;
        }else if($node->nodeName == 'cobr'){
            return $node;
        }else if($node->nodeName == 'pag'){
            return $node;
        }

        return self::getParentNode($node->parentNode);
    }

    /**
     * @param string $xml
     * @return DOMDocument
     */
    public static function getFormatedXml(string $xml)
    {
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
        $xml = $dom->saveXml();

        $domSearch = new DOMDocument('1.0', 'utf-8');
        $domSearch->preserveWhiteSpace = false;
        $domSearch->formatOutput = true;
        $domSearch->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);

        return $domSearch;
    }
}