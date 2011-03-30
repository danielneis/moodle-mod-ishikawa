<?php
require_once('Retangulo.class.php');
require_once('Linha.class.php');

class Ishikawa  {

    var $retangulos = array();

    var $linhas = array();

    var $dados;

    var $inicio_x = 10;
    var $inicio_y = 10;

    var $offset = 50;

    function __construct($dados) {
        $this->dados = $dados;
    }

    function draw() {
        $nivel = 0;
        $maior_altura = 0;

        $ponto_x = $this->inicio_x;
        $ponto_y = $this->inicio_y;

        // Cria retangulos
        foreach ($this->dados as $niveis) {
            foreach ($niveis as $nome_retangulo => $retangulo) {
                $b = new Retangulo($ponto_x, $ponto_y);
                $ponto_x = $b->bottom_x + $this->offset;
                if ($b->bottom_y > $maior_altura) {
                    $maior_altura = $b->bottom_y;
                }
                $this->retangulos[$nivel][$nome_retangulo] = $b;
            }
            $ponto_y = $maior_altura + $this->offset;
            $ponto_x = $this->inicio_x;
            $nivel++;
        }

        // Gera conexões
        foreach ($this->dados as $nivel => $niveis) {
            foreach ($niveis as $nome_retangulo => $retangulo) {
                foreach ($retangulo['conections'] as $connection) {
                    $this->geraLinha($this->retangulos[$nivel][$nome_retangulo],
                                     $this->retangulos[$connection['nivel']][$connection['nome']]);
                }
            }
        }

        $this->printIshikawa();
    }

    function printIshikawa() {
        $img = imagecreatetruecolor(800, 800);
        $green = imagecolorallocate($img, 132, 135, 28);

        foreach ($this->retangulos as $niveis) {
            foreach ($niveis as $retangulo) {
                imagerectangle($img, $retangulo->upper_x, $retangulo->upper_y, $retangulo->bottom_x, $retangulo->bottom_y, $green);
            }
        }

        foreach ($this->linhas as $linha) {
            imageline($img, $linha->xi, $linha->yi, $linha->xf, $linha->yf, $green);
        }

        header('Content-Type: image/jpeg');

        imagejpeg($img);
        imagedestroy($img);
    }

    function geraLinha($origem, $destino) {
        $funcoes = array('pontoMedioTopo', 'pontoMedioBase', 'pontoMedioLateralDireita', 'pontoMedioLateralEsquerda');
        $menor_comprimento_reta = 9999;
        foreach ($funcoes as $funcao1) {
            foreach ($funcoes as $funcao2) {
                list($xi_tmp, $yi_tmp) = call_user_func(array($origem, $funcao1));
                list($xf_tmp, $yf_tmp) = call_user_func(array($destino, $funcao2));
                $comprimento_reta_tmp = $this->comprimentoReta($xi_tmp, $xf_tmp, $yi_tmp, $yf_tmp);

                if ($comprimento_reta_tmp < $menor_comprimento_reta) {
                    $xi = $xi_tmp;
                    $xf = $xf_tmp;
                    $yi = $yi_tmp;
                    $yf = $yf_tmp;
                    $menor_comprimento_reta = $comprimento_reta_tmp;
                }
            }
        }

        $this->linhas[] = new Linha($xi, $xf, $yi, $yf);
    }

    function comprimentoReta($xi, $xf, $yi, $yf) {
        return sqrt(pow($xf-$xi, 2) + pow($yf-$yi,2));
    }

}
?>
