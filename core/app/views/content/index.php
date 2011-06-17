<h2>Gerenciar conteúdo</h2>
<p>
    Selecione qual estrutura você deseja gerenciar.
    <?php tt('Uma estrutura é uma área do site,
        como <em>Notícias</em>, <em>Artigos</em> e outros, por exemplo.') ?>
</p>
<?php
$sites = Aust::getInstance()->getStructures();
?>
<?php /* INICIO DO DIV PAINEL GERENCIAR  - É GLOBAL */ ?>
<div class="painel">

    <?php /* TABS */ ?>
    <div class="tabs_area">
        <!-- the tabs -->
        <ul class="tabs">
            <?php foreach( $sites as $site ): ?>
            <li><a href="#"><?php echo $site['Site']['name'] ?></a></li>
            <?php endforeach; ?>
        </ul>
            
    </div>
    <?php /* PANES */ ?>
    <div class="panes">
            <?php
            /*
             * LOOP POR CADA SITE
             */
            foreach( $sites as $site ): ?>
            <div>
                <table border="0" class="pane_listing">
                <?php if( count($site['Structures']) ): ?>
                    <tr class="header">
                        <td class="secoes">Conteúdos</td>
                        <td class="acao">Opções</td>
                        <td class="tipo">Tipo</td>
                    </tr>
                <?php else: ?>
                    <tr class="list">
                        <td class="sem_conteudo">Não há conteúdos nesta área.</td>
                    </tr>
                    </table>
                    <?php
                    continue;
                endif; ?>
                <?php
                /*
                 * LOOP POR CADA ESTRUTURA
                 */
                foreach( $site['Structures'] as $structure ):

                    /*
                     * Use o comando 'continue' para pular o resto do loop atual
                     */
                    unset($modInfo);
                    if(is_file(MODULES_DIR.$structure['tipo'].'/'.MOD_CONFIG)){
                        /*
                         * Pega dados do módulo. $modInfo existe.
                         */
                        include(MODULES_DIR.$structure['tipo'].'/'.MOD_CONFIG);

                        $type = $modInfo['nome'];
                    } else {
                        $type = $structure['tipo'];
                    }

											$module = null;
											if( !empty($structure['masters']) ){

												$module = Aust::getInstance()->getStructureInstance($structure['id']);
												$relatedAndVisible = $module->getStructureConfig('related_and_visible');
												if( !empty($relatedAndVisible)
														&& !$relatedAndVisible )
													continue;
						
											}

                    if( !StructurePermissions::getInstance()->verify($structure['id']) )
                        continue;
                    ?>
                    
                    <tr class="list">
                        <td class="title">
                            <span><?php echo $structure['nome'] ?></span>
                        </td>
                        <td class="options">
                            <ul>
                            <?php
                            $options = (is_array($modInfo['opcoes'])) ? $modInfo['opcoes'] : Array();
                            foreach ($options as $chave=>$valor) {
                                if( StructurePermissions::getInstance()->verify($structure['id'], $chave) )
                                    echo '<li><a href="adm_main.php?section='.$_GET['section'].'&action='.$chave.'&aust_node='.$structure['id'].'">'.$valor.'</a></li>';
                            }
                            ?>
                            </ul>
                        </td>
                        <td class="tipo">
                            <?php
                            /*
                             * TIPO
                             */
                            echo $type;
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="footer">
                        <td colspan="4"></td>
                    </tr>
                    </table>

                </div>
            <?php
			unset($module);
			endforeach; ?>
    </div>

</div><?php // FIM DO DIV PAINEL GERENCIAR ?>

<br clear="all" />
<br clear="all" />