<?php

/**
 * @summary: Class de abstração para automatizar o processo de "montagem" de uma lista de objetos 
 * ja formatados de acordo com o submit do formulario passando as listas de colunas das datagrids
 * @since 29/05/2024
 * @author Lucas Cid <lucasfelipaaa@gmail.com>
 */
class W5iSerializador 
{
    /**
     * @author Lucas Cid 
     * @since 29/05/2024
     * @summary : Intencao de serializar os dados que vem do submit da datagrid de forma dinamica para obtencao dos objetos formatados.
     * @param array $param : podendo ser tudo que vem do submit ($param recebido no onSave) 
     * ficando por conta da funcao toda a formatacao dos nomes das propriedades do objeto.
     * @param string $datagridId : id da data setado no constructor do fomulario com o metodo setId(), 
     * neste caso, argumento utilizado para retornar o objeto correspondente com a datagrid passada.
     * @return array<stdClass>|bool
     */
    public static function serializarDatagrid($param, string $datagridId) 
    {
        $listas = self::formatSubmitDetailList($param, $datagridId);

        if(isset($listas) && is_array($listas) && !empty($listas)) 
        {
            $keys = array_keys($listas);

            $primeira = $keys[0];

            //obter a quantidade de items para criar a quantidade certa de stdClass (records)
            $primeiraPosicao = $listas[$primeira];

            $quantityObjects = count($primeiraPosicao);
            $stdList = self::createEmptyStdClassListByArrCount($quantityObjects);

            //neste exemplo sao 4 colunas por conta da datagrid passada
            for($superIndex = 0; $superIndex  < count($listas); $superIndex ++) 
            {
                $colunaAtual = $listas[$keys[$superIndex]];

                $chaveAtual  = $keys[$superIndex];
                //percorrendo os elementos da lista atual, exemplo: 
                for($sub = 0 ; $sub < count($colunaAtual); $sub++) 
                {
                    $stdList[$sub]->{$chaveAtual} = $colunaAtual[$sub];
                } 
            }
            return $stdList;
        }
        return false;
    }

    /**
     * @summary : Apenas vai criar uma lista de stdClass vazios de acordo com a quantidade de records que tem na datagrid
     * @author Lucas Cid <lucasfelipaaa@gmail.com>
     * @return array<stdClass>
     */
    private static function createEmptyStdClassListByArrCount(int $count) 
    {
        $list = [];

        for($i = 0; $i < $count; $i++) 
        {   
            $std = new stdClass;
            $list[] = $std;
        }
        return $list;
    }

    /**
     * @summary : Formata todos os arrays que são provenientes do submit do formulario presentes no detalhe, 
     * o adianti configura o array de colunas da seguinte forma: "filter_datagrid_<nome da coluna>", 
     * apenas formatando de forma dinamica, removendo este prefixo
     * @author Lucas Cid
     * @return array<object>|bool
     */
    private static function formatSubmitDetailList($param, $datagridId) 
    {

        $stringLenForSubstring = strlen($datagridId."_");

        $arrayOfColumns = array();

        $keys = array_keys($param);
        
        if(!isset($keys))
        {
            return false;
        }
        foreach($keys as $key) 
        {
            if(str_contains($key, $datagridId."_")) 
            {
                $newFormattedKey = substr($key, $stringLenForSubstring, strlen($key));
                $arrayOfColumns =  array_merge([ $newFormattedKey => $param[$key] ], $arrayOfColumns);
            }
        }
        return $arrayOfColumns;
    }
}