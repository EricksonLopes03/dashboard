<?php 
    Class Dashboard{
        private $dataInicio;
        private $dataFim;
        private $numeroVendas;
        private $totalVendas;
        // private $clientesAtivos;
        // private $clientesInativos;
        // private $totalReclamacoes;
        // private $totalElogios;
        // private $totalSugestoes;
        // private $totalDespesas;

    

        public function __get($name)
        {
            return $this->$name;
        }

        public function __set($name, $value)
        {
            $this->$name = $value;
        }

    }

    class Conexao{
        private $dsn = 'mysql:host=mysql;dbname=dashboard';
        private $usuario = 'root';
        private $senha = 'password';
        private $conexao = null;

        public function conectar(){
            try {
                if($this->conexao === null){
                    $this->conexao = new PDO($this->dsn, $this->usuario, $this->senha);
                    $this->conexao->exec('set charset set utf8');
                }
                return $this->conexao;
                    
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
    }

    class Dao {
        private $dashboard;
        private $conexao;

        public function __construct (Dashboard $dashboard, Conexao $conexao){
            $this->conexao = $conexao->conectar();
            $this->dashboard = $dashboard;          

        }

        public function getNumeroVendas(){
            $query = "select count(*) as numerovendas from tb_vendas where data_venda BETWEEN :data_inicio and :data_fim;";
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':data_inicio', $this->dashboard->__get('dataInicio'));
            $stmt->bindValue(':data_fim', $this->dashboard->__get('dataFim'));
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ)->numerovendas;

        }

        public function getTotalVendas(){
            $query = 'select sum(total) as totalvendas from tb_vendas where data_venda BETWEEN :data_inicio and :data_fim;';
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':data_inicio', $this->dashboard->__get('dataInicio'));
            $stmt->bindValue(':data_fim', $this->dashboard->__get('dataFim'));
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ)->totalvendas;

        }

    }

    $dashboard = new Dashboard();
    $conexao = new Conexao();
    $data = explode('-', $_GET['competencia']);
    $ano = $data[0];
    $mes = $data[1];
    $dia = date('t', mktime(0, 0, 0, $mes, 1, $ano));
    $dashboard->__set('dataInicio', $ano .'-'. $mes. '-01');
    $dashboard->__set('dataFim', $ano .'-'. $mes. '-' . $dia);
    $dao = new Dao($dashboard, $conexao);
    $dashboard->__set('numeroVendas', $dao->getNumeroVendas());
    $dashboard->__set('totalVendas', $dao->getTotalVendas());
    echo json_encode($dashboard);
   //print_r($dashboard);
    


?>