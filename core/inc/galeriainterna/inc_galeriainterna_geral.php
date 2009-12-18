<?php
/************************************
*
*	Galeria interna. Com esta, é possível inserir imagens no meio dos textos
*
*
************************************/

header("Cache-Control: no-cache, must-revalidate");

session_id(strip_tags($_GET['sid']));
session_start();
//echo $_SESSION["login1"];

include("../../../conn.php");
include("../../../conf.php");
include("../../../func.php");

        if (isset($acao)){
            	if ((!empty($arquivopeq)) AND ($arquivopeq <> "none")){
	
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
			
	                $fppeq = fopen($arquivopeq,"rb");
	                $arquivo_temppeq = fread($fppeq, filesize($arquivopeq));
	                fclose($fppeq);
	
					$im = imagecreatefromstring($arquivo_temppeq); //criar uma amostra da imagem original
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
					imagejpeg($nova, '', 79);
					$mynewimage = ob_get_contents();               
					ob_end_clean(); 

					$arquivo_temppeq = addslashes($mynewimage);

					$sqlpeq = "INSERT INTO imagens(bytes,dados,nome,tipo,especie,
													adddia,addmes,addano,addhora,addminuto,
													autorid,autornome)
								VALUES('".strlen($mynewimage)."','$arquivo_temppeq','$arquivopeq_name','$arquivopeq_type','galeriainterna',
													'".PegaData('dia')."','".PegaData('mes')."','".PegaData('ano')."','".PegaData('hora')."','".PegaData('minuto')."',
													'".$_SESSION["loginid"]."','".$_SESSION["loginnome"]."')";
					//echo $sqlpeq;
					if ($conexao->exec($sqlpeq)){
					  $mystatus = "<p style=\"color:green\">Ítem inserido com sucesso.</p>";
					} else
					  $mystatus = "<p style=\"color:red\">Erro ao inserir ítem. Contate um administrador do site.</p>";
	            } else {
		            $mystatus = "<p style=\"color:red\">Selecione uma imagem.</p>";
                }
        }

	if(isset($mydelete)){
		if ($mydelete == "yes"){
			$sqldel = "DELETE FROM imagens WHERE id=$fotoid";
            if ($conexao->query($sqldel)){
		      $mystatus = "<p style=\"color:green; \">Ítem apagado com sucesso.</p>";
	        } else
		      $mystatus = "<p style=\"color:red\">Erro ao apagar ítem.</p>";
			
			
			
		}	
	}

?>


<html>
<head>
<title>Galeria de Imagens</title>

<style>
a   { color: blue }
p   { margin: 0px }
</style>
</head>

<body topmargin="0" leftmargin="0" rightmargin="0">

<div style="width:100%; background: #E6FFF2; padding-left: 3px; padding-top: 2px; text-align: center;">
    Com <strong>Banco de Imagens</strong> você pode inserir imagens no meio do seu texto, tanto em notícias como em menus.
    
    Para isto, clique no link "Adicionar nova imagem", escolha a imagem e insira ela no sistema.
    Feito isto, a imagem ganhará um número chamado de <strong>ID</strong> (iniciais de "<strong>id</strong>entificação"). Copie o código em laranja da
    imagem desejada e cole no meio do seu texto. É recomendado usar o comando [quebradelinha] caso a imagem e o texto fiquem na mesma linha.
</div>
<div style="width:100%; background:#FFFFC4; padding-left: 3px; padding-top: 2px; text-align: center;">
    <a href="inc_galeriainterna_add.php?sid=<?php echo session_id();?>">Adicionar nova imagem</a>
    <br />
    <?php if(!empty($mystatus)) echo $mystatus; ?>
</div>

<?php //=$_SESSION["associd"]; ?>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
	<?php
//	echo "$myid";
	$currtd = 0;
	if (empty($maxtd)) $maxtd = 5;
	
	$autor = "AND autorid='".$_SESSION["loginid"]."'";
	$sqlgal = "SELECT * FROM imagens WHERE especie='galeriainterna' ORDER BY id DESC LIMIT 0,12";


	$mysqlgal = $conexao->query($sqlgal);

	if (!empty($mysqlgal) AND count($mysqlgal) > 0){
		foreach( $mysqlgal as $thumbs) {
			$ordem = $thumbs["ordem"];
			$fotoid = $thumbs["id"];
			if ($currtd == $maxtd){
				$currtd = 0;
				echo "</tr>";
				echo "<tr>";
			}
			$currtd = $currtd + 1;
			echo "<td>";
			echo "<center>";
			
				
				echo "<img style=\"border-color:black;\" width=\"60\" src=\"../visualiza_foto.php?myid=".$fotoid."&table=imagens&thumbs=yes&xsize=60\" border=\"1\">";
				echo "<br />";
				echo "<p style=\"font-size: 12px;\"><strong>ID:</strong> $fotoid</p>";
				echo '<p style="font-size: 11px;">Código</p>';
				echo '<p style="font-size: 11px; color: orange">[img id='.$fotoid.' img]</p>';
				echo "<a href=\"inc_galeriainterna_geral.php?fotoid=$fotoid&mydelete=yes&sid=". session_id() ."\">";
				echo "Deletar";
				echo "</a>";
			echo "</center>";
			echo "</td>";
		
		}
	} else { ?>
    <div style="width:100%; height: 25px; background:white; padding-left: 3px; padding-top: 2px; text-align: center;">
        Nenhuma imagem cadastrada.
    </div>
    <?php
	}
	?>
</tr>
</table>

</body>
</html>
