<h2>Atualização de Categoria</h2>

<?php

/*
 * ATUALIZA CATEGORIAS
 */
/**
 * Inicializa variáveis
 */
//$status_imagem = false;

/**
 * Verifica se um arquivo foi realmente enviado. Se não foi, assegura-se de
 * excluir a variável que poderia ter informações sobre algum arquivo.
 */
if(!empty($_FILES['arquivo'])){
    if(empty($_FILES['arquivo']['name']) OR empty($_FILES['arquivo']['type'])){
        $_FILES['arquivo'] = array();
    }
}


/**
 * Se uma imagem foi enviada, faz todo o processamento
 * @todo - fazer funcionar com PHP-PDO
 */
if(!empty($_FILES['arquivo'])){

    echo '<p>Arquivo existe</p>';
    $frmarquivo = $_FILES['arquivo'];
    // função de ajuste da imagem
    function fastimagecopyresampled (&$dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h, $quality) {
        if (empty($src_image) || empty($dst_image)) { return false; }
        if ($quality <= 1) {
            $temp = imagecreatetruecolor ($dst_w + 1, $dst_h + 1);
            imagecopyresized ($temp, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w + 1, $dst_h + 1, $src_w, $src_h);
            imagecopyresized ($dst_image, $temp, 0, 0, 0, 0, $dst_w, $dst_h, $dst_w, $dst_h);
            imagedestroy ($temp);
        } elseif ($quality < 5 && (($dst_w * $quality) < $src_w || ($dst_h * $quality) < $src_h)) {
            $tmp_w = $dst_w * $quality;
            $tmp_h = $dst_h * $quality;
            $temp = imagecreatetruecolor ($tmp_w + 1, $tmp_h + 1);
            imagecopyresized ($temp, $src_image, $dst_x * $quality, $dst_y * $quality, $src_x, $src_y, $tmp_w + 1, $tmp_h + 1, $src_w, $src_h);
            imagecopyresampled ($dst_image, $temp, 0, 0, 0, 0, $dst_w, $dst_h, $tmp_w, $tmp_h);
            imagedestroy ($temp);
        } else {
            imagecopyresampled ($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
        }
        return true;
    }

    $fppeq = fopen($frmarquivo['tmp_name'],"rb");
    $arquivo = fread($fppeq, filesize($frmarquivo['tmp_name']));
    fclose($fppeq);

    $im = imagecreatefromstring($arquivo); //criar uma amostra da imagem original
    //echo $arquivo;

    $largurao = imagesx($im);// pegar a largura da amostra
    $alturao = imagesy($im);// pegar a altura da amostra


    if($largurao > 800)
        $largurad = 800;
    else
        $largurad = $largurao; // definir a altura da miniatura em px
    $alturad = ($alturao*$largurad)/$largurao;// calcula a largura da imagem a partir da altura da miniatura
    $nova = imagecreatetruecolor($largurad,$alturad);//criar uma imagem em branco
    // MODO RÁPIDO E POUCA QUALIDADE: copiar sobre a imagem em branco a amostra diminuindo conforma as especificações da miniatura
    //fastimagecopyresampled($nova,$im,0,0,0,0,$largurad,$alturad,$largurao,$alturao,5);
    // MODO LENTO E BASTANTE QUALIDADE: copiar sobre a imagem em branco a amostra diminuindo conforma as especificações da miniatura
    imagecopyresampled($nova,$im,0,0,0,0,$largurad,$alturad,$largurao,$alturao);

    ob_start();
    imagejpeg($nova, '', 76);
    $mynewimage = ob_get_contents();
    ob_end_clean();


    if(strlen($mynewimage) > "5000000"){

        echo "<p>A imagem ultrapassa o tamanho máximo de 5Mb.</p>";

    } else {
        $arquivo_temppeq = addslashes($mynewimage);

        $sql = "INSERT INTO imagens(bytes,dados,nome,tipo,ref,classe,adddate,autor)
                    VALUES('".strlen($mynewimage)."','$arquivo_temppeq','".$frmarquivo['name']."','".$frmarquivo['type']."','$w','categorias','".date("Y-m-d H:i:s")."',".$_POST['autorid'].")";


        //echo "$sql";
        echo "Tamanho do arquivo: ".$arquivo_size."bytes.";

        // insere no DB
        if ($conexao->exec($sql)){
            $status_imagem = true;
        } else {
            $status_imagem = false;
        }
    }

}



/**
 * Ajusta dados vindos via POST para criar sql
 */
$texto = $_POST['frmdescricao'];
$texto = str_replace("\"","\"", $texto);
$texto = str_replace("'","\'", $texto);


$sql = "UPDATE categorias
        SET
            nome='".$_POST['frmnome']."',
            descricao='".$texto."'
        WHERE
            id='".$_POST['w']."'
        ";

//							echo $sql;
if(!empty($status_imagem) AND $status_imagem == true){
    echo '<p>Nova imagem salva com sucesso!</p>';
} elseif( !empty($status_imagem) ) {
    echo '<p>Houve um erro desconhecido ao salvar a imagem. Contate o administrador.</p>';
}

$result = $conexao->exec($sql);

if ( $result > 0 ){
    echo '<p style="color: green;">As informações foram salvas com sucesso!</p>';
    echo '
        <p>
            <a href="adm_main.php?section='.$_GET['section'].'&action=list_content"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
        </p>';

} elseif ( is_int($result) AND $result == 0 ){
    echo '<p style="color: green;">Os valores entrados já existém. Nenhuma alteração feita.</p>';
    echo '
        <p>
            <a href="adm_main.php?section='.$_GET['section'].'&action=list_content"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
        </p>';
} else {
    echo '<p style="color: red;">Ocorreu um erro desconhecido ao salvar as informações. Tente novamente.</p>';
    echo '
        <p>
            <a href="adm_main.php?section='.$_GET['section'].'"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
        </p>';
}


?>
