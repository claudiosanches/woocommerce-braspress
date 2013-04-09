<?php
/**
 * Page-level DocBlock
 * Classe base para os serviços de cálculo de frete da Braspress.
 *
 * @author Ivan Wilhelm <ivan.whm@me.com>
 * @package braspress
 * @version 1.0
 * @abstract
 */
  abstract class Braspress
  {
    /**
     * URL do calculador de frete da Braspress.
     * @access public
     */

    const URL_CALCULADOR = 'http://tracking.braspress.com.br/wscalculafreteisapi.dll/wsdl/IWSCalcFrete?wsdl';
    /**
     * Contém o tipo de frete rodoviário.
     * @access public
     */
    const TIPO_FRETE_RODOVIARIO = '1';
    /**
     * Contém o tipo de frete aéreo.
     * @access public
     */
    const TIPO_FRETE_AEREO = '2';

    /**
     * Contém o CNPJ da empresa que possui contrato com a Braspress.
     * @var double
     * @access private
     */
    private $cnpjEmpresa;

    /**
     * Cria um objeto da Braspress.
     * @param double $cnpjEmpresa CNPJ da empresa que possui contrato com a Braspress.
     * @access public
     */
    public function __construct($cnpjEmpresa)
    {
      $this->cnpjEmpresa = $cnpjEmpresa;
    }

    /**
     * Retorna o CNPJ da empresa que possui contrato com a Braspress.
     * @return double
     * @access protected
     */
    protected function getCnpjEmpresa()
    {
      return (double) $this->cnpjEmpresa;
    }

    /**
     * Realiza o processamento da consulta.
     *
     * @return boolean
     * @access public
     * @abstract
     */
    abstract public function processaConsulta();
  }
