<?php

/**
 * Classe que disponibilizar� mensagens de erro para o tratamento de exce��es
 * @author Carlos Domingues <carlos.domingues@grupoapi.net.br>
 */
class MensagensErroService
{
    protected $mensagens = array();

    public function __construct()
    {
        $this->_configMensagens();
    }

    public function getMensagem($cod)
    {
        if (isset($this->mensagens[$cod])) {
            return $this->mensagens[$cod];
        }

        return false;
    }

    protected function _addMensagem($cod, $mensagem)
    {
        $this->mensagens[$cod] = $mensagem;
    }

    private function _configMensagens()
    {
        $this->_addMensagem(902, "Usu�rio n�o autenticado ou hash inv�lido.");
        $this->_addMensagem(903, "Faltou o campo '[PARAMETRO]', ou est� incorreto.");
        $this->_addMensagem(904, "Faltou o campo '[PARAMETRO]'.");
        $this->_addMensagem(906, "O nome do arquivo para cadastrar upload tem que ser definido corretamente.");
        $this->_addMensagem(907, "Este m�todo pode ser acessado somente pelo cliente.");
        $this->_addMensagem(908, "Este m�todo pode ser acessado somente pelo correspondente.");
        $this->_addMensagem(911, "Este campo n�o pode ficar em branco.");
        $this->_addMensagem(912, "Este campo deve ter no m�nimo [QUANTIDADE] caracteres.");
        $this->_addMensagem(913, "Este campo n�o foi definido corretamente. Permitido: [VALORES_PERMITIDOS].");
        $this->_addMensagem(914, "Este campo n�o deve ter mais que [QUANTIDADE] caracteres.");
        $this->_addMensagem(915, "Este campo possui uma data inv�lida.");
    }
}