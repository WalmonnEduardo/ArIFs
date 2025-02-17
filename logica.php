<?php
require_once("arquivos/Texto.php");
require_once("arquivos/Ar.php");
function menu()
{
    $ars = json_decode(file_get_contents(__DIR__."/data/arcondicionado.json"),true);
    if($ars == null)
    {
        $ars = array();
    }

    system("clear");
    $itens = ["Cadastrar ar","Mostrar ar","Definir ar","Remover ar","Finalizar"];
    Texto::montar_tabela($itens);
    $esc = readline("Escolha: ");
    switch($esc)
    {
        case 1:
            cadar();
            menu();
        break;
        case 2:
            mostrar();
        break;
        case 3:
            selectar();
        break;
        case 4:
            remover();
        break;
        case 5:
            die;
        break;
        default:
            menu();
        break;
    }
}
function cadar()
{
    $dados = pegardados();
    system("clear");
    $nome = readline("Nome: ");
    if($nome != null)
    {
        $ar = new Ar();
        $ar->setId($dados["id"]);
        $ar->setNome($nome);
        $dados["id"] += 1;
        $dados["ars"][] = $ar;
    }
    devolverdados($dados);
}
function mostrar()
{
    system("clear");
    $dados = pegardados();
    if(count($dados["ars"]) == 0)
    {
        print "\033[31m Não tem arcondicionado cadastrado!!! \033[m\n";
        readline("Aperte enter para voltar ao menu principal!");
        menu();
    }
    $nomes = array();
    foreach($dados["ars"] as $ar)
    {
        $nomes[] = $ar->getNome();
    }
    $nomes[] = "Mostrar todos";
    $nomes[] = "Finalizar";
    Texto::montar_tabela($nomes);
    $esc = readline("Escolha: ");
    if($esc == count($dados["ars"])+2)
    {
        menu();
    }
    else if($esc == count($dados["ars"])+1)
    {
        $esc--;
        system("clear");
        foreach($dados["ars"] as $ar)
        {

            for($i = 0 ; $i < 40 ;$i++)
            {
                print "=";
            }
            print "\n\n";
            print $ar;
        }
        for($i = 0 ; $i < 40 ;$i++)
        {
            print "=";
        }
        print "\n\n";
        readline("Aperte enter para voltar!!!");
        mostrar();
    }
    else if($esc > 0 && $esc <= count($dados["ars"]))
    {
        $esc--;
        system("clear");
        print $dados["ars"][$esc];
        readline("");
        mostrar();
    }
    else
    {
        mostrar();
    }
}
function selectar()
{
    system("clear");
    $dados = pegardados();
    if(count($dados["ars"]) == 0)
    {
        print "\033[31m Não tem arcondicionado cadastrado!!! \033[m\n";
        readline("Aperte enter para voltar ao menu principal!");
        menu();
    }
    $nomes = array();
    foreach($dados["ars"] as $ar)
    {
        $nomes[] = $ar->getNome();
    }
    $nomes[] = "Finalizar";
    Texto::montar_tabela($nomes);
    $esc = readline("Escolha: ");
    if($esc == count($dados["ars"])+1)
    {
        menu();
    }
    else if($esc > 0 && $esc <= count($dados["ars"]))
    {
        $esc--;
        $dados["ars"][$esc] = altstatus($dados["ars"][$esc]);
        devolverdados($dados);
        selectar();
    }
    else
    {
        selectar();
    }
}
function altstatus(Ar $ar)
{
    system("clear");
    print $ar;
    $itens = ["Nome","Temperatura","Ocilação","Ligado/Desligado","Finalizar"];
    Texto::montar_tabela($itens);
    $esc = readline("Escolha o que quer editar: ");
    switch($esc)
    {
        case 1:
            $nome = readline("Digite o novo nome: ");
            if($nome != null)
            {
                $ar->setNome($nome);
            }
            return altstatus($ar);
        break;
        case 2:
            if($ar->isOnoff() == false)
            {
                print "Ligue o arcondicionado antes de definir a temperatura\n";
                readline("Aperte enter para continaur");
                return altstatus($ar);
            }
            else
            {
                $temperatura = readline("Digite o nova temperatura de 15 a 30: ");
                if($temperatura != null && is_numeric($temperatura) && $temperatura > 15  && $temperatura < 31)
                {
                    $ar->setTemperatura($temperatura);
                }
                return altstatus($ar);
            }
        break;
        case 3:
            if($ar->isOnoff() == false)
            {
                print "Ligue o arcondicionado antes de definir para ele ocilar\n";
                readline("Aperte enter para continaur");
                return altstatus($ar);
            }
            else
            {
                if($ar->isOcilacao() == false)
                {
                    $ar->setOcilacao(true);
                }
                else
                {
                    $ar->setOcilacao(false);
                }
                return altstatus($ar);
            }
            
        break;
        case 4:
            if($ar->isOcilacao() == true)
            {
                $ar->setOcilacao(false);
            }
            if($ar->isOnoff() == false)
            {
                $ar->setOnoff(true);
            }
            else
            {
                $ar->setOnoff(false);
            }
            return altstatus($ar);
        break;
        case 5:
            return $ar;
        break;
        default:
            return altstatus($ar);
        break;
    }
}
function remover()
{
    system("clear");
    $dados = pegardados();
    if(count($dados["ars"]) == 0)
    {
        print "\033[31m Não tem arcondicionado cadastrado!!! \033[m\n";
        readline("Aperte enter para voltar ao menu principal!");
        menu();
    }
    $nomes = array();
    foreach($dados["ars"] as $ar)
    {
        $nomes[] = $ar->getNome();
    }
    $nomes[] = "Finalizar";
    Texto::montar_tabela($nomes);
    $esc = readline("Escolha: ");
    if($esc == count($dados["ars"])+1)
    {
        menu();
    }
    else if($esc > 0 && $esc <= count($dados["ars"]))
    {
        $esc--;
        unset($dados["ars"][$esc]);
        $dados["ars"] = array_values($dados["ars"]);
        devolverdados($dados);
        remover();
    }
    else
    {
        remover();
    }
}
function pegardados()
{
    if(file_exists(__DIR__."/data/arcondicionado.json"))
    {
        $dados = json_decode(file_get_contents(__DIR__."/data/arcondicionado.json"),true);
    }
    else
    {
        $dados = null;
    }
    if($dados == null)
    {
        $dados["ars"] = array();
        $dados["id"] = 1;
    }
    else
    {
        $dados["ars"] = Ar::objAr($dados["ars"]);
    }
    return $dados;
}
function devolverdados($dados)
{
    if (!file_exists(__DIR__.'/data'))
    {
        mkdir(__DIR__.'/data', 0777, true);
    }
    $dados = json_encode($dados,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents(__DIR__."/data/arcondicionado.json",$dados);  
}
menu();
?>
