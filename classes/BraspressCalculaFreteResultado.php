<?php

  /**
   * Classe que irá conter o resultado do cálculo de frete monstrando todos
   * os detalhes do cálculo.
   *
   * @author Ivan Wilhelm <ivan.whm@me.com>
   * @package braspress
   * @version 1.0
   * @final
   */
  final class BraspressCalculaFreteResultado
  {

    /**
     * Contém o valor total do frete.
     * @var double
     * @access private
     */
    private $totalFrete;

    /**
     * Contém o percentual de ICMS do frete.
     * @var double
     * @access private
     */
    private $icms;

    /**
     * Contém o valor do ICMS do frete.
     * @var double
     * @access private
     */
    private $valorIcms;

    /**
     * Contém o valor do frete por peso.
     * @var double
     * @access private
     */
    private $fretePeso;

    /**
     * Contém o valor do frete por valor.
     * @var double
     * @access private
     */
    private $freteValor;

    /**
     * Contém a taxa de cadastro da seção.
     * @var double
     * @access private
     */
    private $taxaSecaoCad;

    /**
     * Contém a taxa de pedágio.
     * @var double
     * @access private
     */
    private $taxaPedagio;

    /**
     * Contém a taxa de despacho.
     * @var double
     * @access private
     */
    private $taxaDespacho;

    /**
     * Contém a taxa de ITR.
     * @var double
     * @access private
     */
    private $taxaITR;

    /**
     * Contém outras taxas.
     * @var double
     * @access private
     */
    private $taxaOutros;

    /**
     * Contém a taxa de ademe.
     * @var double
     * @access private
     */
    private $taxaAdeme;

    /**
     * Contém o valor do subtotal do frete.
     * @var double
     * @access private
     */
    private $subtotal;

    /**
     * Contém a mensagem de erro, caso tenha havido
     * @var string
     * @access private
     */
    private $mensagemErro;

    /**
     * Contém o prazo de entrega do frete.
     * @var double
     * @access private
     */
    private $prazoEntrega;

    /**
     * Indica se a consulta foi realizada com sucesso.
     * @var boolean
     * @access private
     */
    private $sucesso;

    /**
     * Cria um objeto de resultado.
     * @param stdClass $retorno Retorno da Braspress.
     * @access public
     */
    public function __construct(stdClass $retorno)
    {
      $this->setTotalFrete(isset($retorno->TOTALFRETE) ? $retorno->TOTALFRETE : 0);
      $this->setIcms(isset($retorno->ICMS) ? $retorno->ICMS : 0);
      $this->setValorIcms(isset($retorno->VALORICMS) ? $retorno->VALORICMS : 0);
      $this->setFretePeso(isset($retorno->FRETEPESO) ? $retorno->FRETEPESO : 0);
      $this->setFreteValor(isset($retorno->FRETEVALOR) ? $retorno->FRETEVALOR : 0);
      $this->setTaxaSecaoCad(isset($retorno->TXSECCAD) ? $retorno->TXSECCAD : 0);
      $this->setTaxaPedagio(isset($retorno->TXPEDAGIO) ? $retorno->TXPEDAGIO : 0);
      $this->setTaxaDespacho(isset($retorno->TXDESPACHO) ? $retorno->TXDESPACHO : 0);
      $this->setTaxaITR(isset($retorno->TXITR) ? $retorno->TXITR : 0);
      $this->setTaxaOutros(isset($retorno->TXOUTROS) ? $retorno->TXOUTROS : 0);
      $this->setTaxaAdeme(isset($retorno->TXADEME) ? $retorno->TXADEME : 0);
      $this->setSubtotal(isset($retorno->SUBTOTAL) ? $retorno->SUBTOTAL : 0);
      $this->setPrazoEntrega(isset($retorno->PRAZOENTREGA) ? $retorno->PRAZOENTREGA : 0);
      $mensagemErro = trim(isset($retorno->MSGERRO) ? $retorno->MSGERRO : '');
      $this->setMensagemErro(($mensagemErro == 'OK') ? '' : $mensagemErro);
      $this->setSucesso($mensagemErro == 'OK');
    }

    /**
     * Informa o valor total do frete.
     * @param double $totalFrete Valor total do frete.
     * @access private
     */
    private function setTotalFrete($totalFrete)
    {
      $this->totalFrete = (double) $totalFrete;
    }

    /**
     * Informa o percentual de ICMS do frete.
     * @param double $icms Percentual de ICMS
     * @access private
     */
    private function setIcms($icms)
    {
      $this->icms = (double) $icms;
    }

    /**
     * Informa o valor do ICMS do frete.
     * @param double $valorIcms Valor do ICMS do frete.
     * @access private
     */
    private function setValorIcms($valorIcms)
    {
      $this->valorIcms = (double) $valorIcms;
    }

    /**
     * Informa o valor do frete por peso.
     * @param double $fretePeso Valor do frete por peso.
     * @access private
     */
    private function setFretePeso($fretePeso)
    {
      $this->fretePeso = (double) $fretePeso;
    }

    /**
     * Informa o valor do frete por valor.
     * @param double $freteValor Valor do frete por valor.
     * @access private
     */
    private function setFreteValor($freteValor)
    {
      $this->freteValor = (double) $freteValor;
    }

    /**
     * Informa o valor da taxa da seção cadastro.
     * @param double $taxaSecaoCad Taxa da seção cadastro.
     * @access private
     */
    private function setTaxaSecaoCad($taxaSecaoCad)
    {
      $this->taxaSecaoCad = (double) $taxaSecaoCad;
    }

    /**
     * Informa o valor da taxa de pedágio.
     * @param double $taxaPedagio Taxa de pedágio.
     * @access private
     */
    private function setTaxaPedagio($taxaPedagio)
    {
      $this->taxaPedagio = (double) $taxaPedagio;
    }

    /**
     * Informa o valor da taxa de despacho.
     * @param double $taxaDespacho Taxa de despacho.
     * @access private
     */
    private function setTaxaDespacho($taxaDespacho)
    {
      $this->taxaDespacho = (double) $taxaDespacho;
    }

    /**
     * Informa o valor da taxa de ITR.
     * @param double $taxaITR Taxa de ITR.
     * @access private
     */
    private function setTaxaITR($taxaITR)
    {
      $this->taxaITR = (double) $taxaITR;
    }

    /**
     * Informa o valor das outras taxas.
     * @param double $taxaOutros Outras taxas.
     * @access private
     */
    private function setTaxaOutros($taxaOutros)
    {
      $this->taxaOutros = (double) $taxaOutros;
    }

    /**
     * Informa o valor da taxa de ademe.
     * @param double $taxaAdeme Taxa de ademe.
     * @access private
     */
    private function setTaxaAdeme($taxaAdeme)
    {
      $this->taxaAdeme = (double) $taxaAdeme;
    }

    /**
     * Informa o valor do subtotal do frete.
     * @param double $subtotal Valor do subtotal do frete.
     * @access private
     */
    private function setSubtotal($subtotal)
    {
      $this->subtotal = (double) $subtotal;
    }

    /**
     * Informa a mensagem de erro, caso tenha havido erro.
     * @param string $mensagemErro Mensagem de erro, caso tenha havido erro.
     * @access private
     */
    private function setMensagemErro($mensagemErro)
    {
      $this->mensagemErro = (string) $mensagemErro;
    }

    /**
     * Informa o prazo de entrega do frete.
     * @param double $prazoEntrega Prazo de entrega do frete.
     * @access private
     */
    private function setPrazoEntrega($prazoEntrega)
    {
      $this->prazoEntrega = (double) $prazoEntrega;
    }

    /**
     * Indica se a consulta foi concluída com sucesso.
     * @param boolean $sucesso Indica se a consulta foi concluída com sucesso.
     * @access private
     */
    private function setSucesso($sucesso)
    {
      $this->sucesso = (boolean) $sucesso;
    }

    /**
     * Retorna o valor total do frete.
     * @return double
     * @access public
     */
    public function getTotalFrete()
    {
      return $this->totalFrete;
    }

    /**
     * Retorna o percentual de ICMS.
     * @return double
     * @access public
     */
    public function getIcms()
    {
      return $this->icms;
    }

    /**
     * Retorna o valor do ICMS.
     * @return double
     * @access public
     */
    public function getValorIcms()
    {
      return $this->valorIcms;
    }

    /**
     * Retorna o valor do frete por peso.
     * @return double
     * @access public
     */
    public function getFretePeso()
    {
      return $this->fretePeso;
    }

    /**
     * Retorna o valor do frete por valor.
     * @return double
     * @access public
     */
    public function getFreteValor()
    {
      return $this->freteValor;
    }

    /**
     * Retorna o valor da taxa de seção cadastro.
     * @return double
     * @access public
     */
    public function getTaxaSecaoCad()
    {
      return $this->taxaSecaoCad;
    }

    /**
     * Retorna o valor da taxa de pedágio.
     * @return double
     * @access public
     */
    public function getTaxaPedagio()
    {
      return $this->taxaPedagio;
    }

    /**
     * Retorna o valor da taxa de despacho.
     * @return double
     * @access public
     */
    public function getTaxaDespacho()
    {
      return $this->taxaDespacho;
    }

    /**
     * Retorna o valor da taxa de ITR.
     * @return double
     * @access public
     */
    public function getTaxaITR()
    {
      return $this->taxaITR;
    }

    /**
     * Retorna o valor das outras taxas.
     * @return double
     * @access public
     */
    public function getTaxaOutros()
    {
      return $this->taxaOutros;
    }

    /**
     * Retorna o valor da taxa de ademe.
     * @return double
     * @access public
     */
    public function getTaxaAdeme()
    {
      return $this->taxaAdeme;
    }

    /**
     * Retorna o valor do subtotal do frete.
     * @return double
     * @access public
     */
    public function getSubtotal()
    {
      return $this->subtotal;
    }

    /**
     * Retorna a mensagem de erro, caso tenha havido erro.
     * @return string
     * @access public
     */
    public function getMensagemErro()
    {
      return $this->mensagemErro;
    }

    /**
     * Retorna o prazo de entrega do frete.
     * @return double
     * @access public
     */
    public function getPrazoEntrega()
    {
      return $this->prazoEntrega;
    }

    /**
     * Indica se a consulta foi concluída com sucesso.
     * @return boolean
     * @access public
     */
    public function getSucesso()
    {
      return $this->sucesso;
    }

  }
