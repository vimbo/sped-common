<?php

namespace NFePHP\Common\Exception;

/**
 * @category   NFePHP
 * @package    NFePHP\Common\Exception
 * @copyright  Copyright (c) 2008-2014
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/sped-common for the canonical source repository
 */
class CertificateException extends \RuntimeException implements ExceptionInterface
{
    public static function unableToRead()
    {
        //return new static('Unable to read certificate, ' . static::getOpenSSLError());
        return new static('Não foi possível ler o certificado, ' . static::getOpenSSLError());
    }

    public static function unableToOpen()
    {
        //return new static('Unable to open certificate, ' . static::getOpenSSLError());
        return new static('Não foi possível abrir o certificado, ' . static::getOpenSSLError());
    }

    public static function signContent()
    {
        /*return new static(
            'An unexpected error has occurred when sign a content, ' . static::getOpenSSLError()
        );*/
        return new static(
        'Ocorreu um erro inesperado ao assinar o conteúdo, ' . static::getOpenSSLError()
    );
    }

    public static function getPrivateKey()
    {
        //return new static('An error has occurred when get private key, ' . static::getOpenSSLError());
        return new static('Erro ao obter a private key, ' . static::getOpenSSLError());
    }

    public static function signatureFailed()
    {
        /*return new static(
            'An error has occurred when verify signature, ' . static::getOpenSSLError()
        );*/
        return new static(
            'Erro ao verificar assinatura, ' . static::getOpenSSLError()
        );
    }
    
    protected static function getOpenSSLError()
    {
        //$error = 'get follow error: ';
        $error = 'Erros: ';
        while ($msg = openssl_error_string()) {
            $error .= "($msg)";
        }
        return $error;
    }
}
