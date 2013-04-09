<?php

  /**
   * Classe para realizar o cálculo de frete monstrando todos os detalhes do cálculo.
   *
   * @author Ivan Wilhelm <ivan.whm@me.com>
   * @package braspress
   * @version 1.0
   * @final
   */
  final class BraspressCalculaFrete extends Braspress
  {

    /**
     * Contém o ID de origem.
     * Este ID é informado pela Braspress no ato da assinatura do contrato.
     * @var double
     * @access private
     */
    private $idOrigem;

    /**
     * Contém o CEP de origem.
     * @var double
     * @access private
     */
    private $cepOrigem;

    /**
     * Contém o CEP de destino.
     * @var double
     * @access private
     */
    private $cepDestino;

    /**
     * Contém o tipo de frete;
     * @var string
     * @access private
     */
    private $tipoFrete;

    /**
     * Contém o CPF/CNPJ de destino.
     * @var double
     * @access private
     */
    private $documentoDestino;

    /**
     * Contém o peso em gramas da mercadoria.
     * @var double
     * @access private
     */
    private $peso;

    /**
     * Contém o valor da NF.
     * @var double
     * @access private
     */
    private $valorNF;

    /**
     * Contém a quantidade de volumes do envio.
     * @var double
     * @access private
     */
    private $volume;

    /**
     * Contém o resultado da consulta
     * @var BraspressCalculaFreteResultado
     * @access private
     */
    private $resultado;

    /**
     * Informa o ID da origem.
     * Este ID é informado pela Braspress no ato da assinatura do contrato.
     * @param double $idOrigem ID de origem.
     * @access public
     */
    public function setIdOrigem($idOrigem)
    {
      $this->idOrigem = $idOrigem;
    }

    /**
     * Informa o CEP de origem para a pesquisa.
     * @param double $cepOrigem CEP de origem.
     * @access public
     * @throws Exception
     */
    public function setCepOrigem($cepOrigem)
    {
      if (strlen(trim($cepOrigem)) == 8)
        $this->cepOrigem = $cepOrigem;
      else
        throw new Exception('CEP de origem inválido.');
    }

    /**
     * Informa o CEP de destino para a pesquisa.
     * @param double $cepDestino CEP de destino.
     * @access public
     * @throws Exception
     */
    public function setCepDestino($cepDestino)
    {
      if (strlen(trim($cepDestino)) == 8)
        $this->cepDestino = $cepDestino;
      else
        throw new Exception('CEP de destino inválido.');
    }

    /**
     * Informa o tipo de frete.
     * @param string $tipoFrete Tipo de frete.
     * @access public
     */
    public function setTipoFrete($tipoFrete)
    {
      if (($tipoFrete === Braspress::TIPO_FRETE_RODOVIARIO) or ($tipoFrete === Braspress::TIPO_FRETE_AEREO))
        $this->tipoFrete = $tipoFrete;
      else
        throw new Exception('Tipo de frete inválido.');
    }

    /**
     * Informa o documento CPF/CNPJ do destinatário da mercadoria.
     * @param double $documentoDestino CPF/CNPJ do destinatário da mercadoria.
     * @access public
     */
    public function setDocumentoDestino($documentoDestino)
    {
      $this->documentoDestino = $documentoDestino;
    }

    /**
     * Informa o peso em gramas da mercadoria.
     * @param double $peso Peso em gramas.
     * @access public
     */
    public function setPeso($peso)
    {
      $this->peso = $peso;
    }

    /**
     * Informa o valor da NF da mercadoria.
     * @param double $valorNF Valor da NF.
     * @access public
     */
    public function setValorNF($valorNF)
    {
      $this->valorNF = $valorNF;
    }

    /**
     * Informa o volume da mercadoria.
     * @param double $volume Volume da mercadoria.
     * @access public
     */
    public function setVolume($volume)
    {
      $this->volume = $volume;
    }

    /**
     * Processa a consulta e retorna VERDADEIRO se foi concluída com sucesso.
     * @return boolean
     * @access public
     * @throws Exception
     */
    public function processaConsulta()
    {
      //Ativa o uso de URL FOpen
      ini_set("allow_url_fopen", 1);
      ini_set("soap.wsdl_cache_enabled", 0);
      //Inicia transação junto a Braspag
      try
      {
        if (@fopen(parent::URL_CALCULADOR, 'r'))
        {
          $soap = new SoapClient(parent::URL_CALCULADOR);
          $retorno = $soap->CalculaFrete((double) $this->getCnpjEmpresa(), (double) $this->idOrigem, (double) $this->cepOrigem, (double) $this->cepDestino, (double) $this->getCnpjEmpresa(), (double) $this->documentoDestino, (string) $this->tipoFrete, (double) $this->peso, (double) $this->valorNF, (double) $this->volume);
          if ($retorno instanceof stdClass)
          {
            $servico = new BraspressCalculaFreteResultado($retorno);
            $this->resultado = $servico;
            return TRUE;
          } else
            return FALSE;
        } else
          return FALSE;
      } catch (SoapFault $sf)
      {
        throw new Exception($sf->getMessage());
      }
    }

    /**
     * Retorna o resultado da consulta.
     * @return BraspressCalculaFreteResultado
     * @access public
     */
    public function getResultado()
    {
      return $this->resultado;
    }

  }
