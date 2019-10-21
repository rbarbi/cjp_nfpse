<?php
class ProdutoService extends BaseService{
    
    private $dao;
    private $contratoDAO;
    private $homologacaoDAO;
            
    function __construct($app) {
        parent::__construct($app);
        $this->dao              = new ProdutoDAO();
        $this->contratoDAO      = new ContratoDAO();
        $this->homologacaoDAO   = new HomologacaoDAO();
    }
    
    function getMotivosRejeicao()
    {
        $dao    = new ProdutoDAO();
        $lista = array();
        $lista[0] = "Escolha a opção";
        foreach ($dao->getMotivosRejeicao() as $id => $motivo) {
            $lista[$motivo['descricao']] = $motivo['motivo'];
        }
        return $lista;
    }
    
    function processaRejeicaoProduto($dados, $idUsuario){        
        $this->dao->getCon()->BeginTrans();
        try {
            foreach ($dados['cadUFRejeitada']['UF'] as $idRequisicao => $dadosUf) {
                foreach ($dadosUf as $idCliente => $listaUf) {
                    foreach ($listaUf as $idUFHomologacao => $lixo) {
                        $motivoRejeicaoUF = $dados['motivoRejeicaoUF'][$idCliente][$idUFHomologacao];
                        $this->dao->doRejeitaUF($idUFHomologacao, $motivoRejeicaoUF, $idUsuario);
                        unset($dados["ufs"][$idRequisicao][$idCliente][$idUFHomologacao]);
                    }
                }
            }
            
            //$this->dao->getCon()->RollbackTrans();
            $this->dao->getCon()->CommitTrans();
            return $dados;
        } 
        catch (Exception $ex) {
            $this->dao->getCon()->RollbackTrans();
            throw $e;
        }
    }
    
    function doCadastraProdutos($listaProdutos, $idUsuario)
    {
        $dadosHomologacao = array();
        foreach ($listaProdutos as $idRequisicao => $dadosRequisicao) {

            $listaDeCadastradas = array();
            $listaComErro       = array();

            $dadosContratoDestino = $this->contratoDAO->getContratoPorRequisicao($idRequisicao);

            foreach ($dadosRequisicao as $idCliente => $produtos) {
                foreach ($produtos as $idProdutoHomologacao => $idProduto) {

                    $this->dao->getCon()->BeginTrans();

                    try {
                        $this->dao->cadastrarProdutoParaCliente($idCliente, $idProduto, $dadosContratoDestino, $idUsuario);
                        $this->homologacaoDAO->homologarProduto($idProdutoHomologacao, $idCliente);

                        //$this->dao->getCon()->RollbackTrans();
                        $this->dao->getCon()->CommitTrans();
                        
                        $listaDeCadastradas[$idCliente][$idProdutoHomologacao]["produto"]  = $idProduto;
                        $listaDeCadastradas[$idCliente][$idProdutoHomologacao]["contrato"] = $dadosContratoDestino[0]["cc_id"];
                    }
                    catch (Exception $e) {
                        pred($e->getMessage());
                        $this->dao->getCon()->RollbackTrans();

                        $motivo = "Não foi possível fazer o cadastro de produto em nosso banco de dados: ({$e->getMessage()})";
                        
                        if (strpos($e->getMessage(), "produto_contrato_idx1") !== false || strpos($e->getMessage(), "cliente_estado_idx3") !== false) {
                            $motivo = "Produto já existe para o cliente neste contrato.";
                        }
                        
                        
                        $listaComErro[$idCliente][$idProdutoHomologacao] = array(
                            "contrato"  => $dadosContratoDestino[0]["cc_id"],
                            "produto"   => $idProduto,
                            "motivo"    => $motivo
                        );
                    }
                }

                $homologada = false;

                $motivo = "";
                if (count($listaDeCadastradas[$idCliente]) === count($produtos)) {
                    $this->homologacaoDAO->homologarRequisicao($idRequisicao);
                    $homologada = true;
                } 
                else {
                    $motivo = "Não foram cadastrados todos os produtos!";
                }

                $dadosHomologacao[$idRequisicao] = array(
                    "homologada"            => $homologada,
                    "motivo"                => $motivo,
                    "listaDeCadastradas"    => $listaDeCadastradas,
                    "listaComErro"          => $listaComErro
                );
            }
        }

        return $dadosHomologacao;
    }
    function doRemoveProdutos($listaProdutos, $idUsuario)
    {
        $dadosHomologacao = array();

        foreach ($listaProdutos as $idRequisicao => $dadosRequisicao) {

            $listaDeRemovidos = array();
            $listaComErro     = array();

            $dadosContratoDestino = $this->contratoDAO->getContratoPorRequisicao($idRequisicao);

            foreach ($dadosRequisicao as $idCliente => $produtos) {
                foreach ($produtos as $idProdutoHomologacao => $idProduto) {

                    $this->dao->getCon()->BeginTrans();

                    try {
                        $this->dao->removerProdutoParaCliente($idCliente, $idProduto, $dadosContratoDestino, $idUsuario);
                        $this->homologacaoDAO->homologarProduto($idProdutoHomologacao, $idCliente);

                        $this->dao->getCon()->CommitTrans();
                        
                        $listaDeRemovidos[$idCliente][$idProdutoHomologacao]["produto"]  = $idProduto;
                        $listaDeRemovidos[$idCliente][$idProdutoHomologacao]["contrato"] = $dadosContratoDestino[0]["cc_id"];
                    }
                    catch (Exception $e) {
                        $this->dao->getCon()->RollbackTrans();

                        $motivo = "Não foi possível fazer o cadastro de produto em nosso banco de dados: ({$e->getMessage()})";
                        
                        if (strpos($e->getMessage(), "produto_contrato_idx1") !== false || strpos($e->getMessage(), "cliente_estado_idx3") !== false) {
                            $motivo = "Produto já existe para o cliente neste contrato.";
                        }
                        
                        
                        $listaComErro[$idCliente][$idProdutoHomologacao] = array(
                            "contrato"  => $dadosContratoDestino[0]["cc_id"],
                            "produto"   => $idProduto,
                            "motivo"    => $motivo
                        );
                    }
                }

                $homologada = false;

                $motivo     = "";
                if (count($listaDeRemovidos[$idCliente]) === count($produtos)) {
                    $this->homologacaoDAO->homologarRequisicao($idRequisicao);
                    $homologada = true;
                } 
                else {
                    $motivo = "Não foram cadastrados todos os produtos!";
                }

                $dadosHomologacao[$idRequisicao] = array(
                    "homologada"            => $homologada,
                    "motivo"                => $motivo,
                    "listaDeRemovidos"      => $listaDeRemovidos,
                    "listaComErro"          => $listaComErro
                );
            }
        }

        return $dadosHomologacao;
    }
}
