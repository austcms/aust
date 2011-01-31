AUSTCMS 0.1.5

O AustCMS foi iniciado em set/2008. Sua arquitetura foi criada baseada na
seguinte estrutura:

      CORE ----- Módulo
       | \
       |  \
       |   \
       |    --- Módulo --- Módulo-Embed
       |           \
     Módulo         ------ Módulo-Embed

O Core do AustCMS é o responsável pela leitura dos diversos módulos, de forma
que se torna fácil a manutenção do sistema.


MÓDULOS e MÓDULOS-EMBED
---------------------------------------

Módulos são partes do sistema que desempenham determinadas funções. Eles são os
responsáveis por quase todas as funcionalidades do CMS. O Aust, com seu core, faz a
leitura destes módulos, chamando suas funções e métodos.

Módulos-Embed são módulos que têm a capacidade de fazer parte de outros
módulos.

Exemplo:
*   O módulo Textos não tem capacidade de lidar com imagens. O Módulo-Embed
    'galeriadeimagens' sim. Como ele é embed, ele pode se encaixar no formulário
    do módulo Textos, dando então a sua funcionalidade para o módulo Textos.

MÓDULOS ATUAIS
---------------------------------------

Atualmente, o sistema conta com os seguintes módulos:

- Agenda: possui interface para controlar datas e horários.
- Arquivos: possibilidade de upload de arquivos.
- Cadastro: este módulo possui capacidade de criar tabelas e campos, de forma
			que pode-se reconstruir virtualmente qualquer domínio sem necessidade
			de tocar em códigos-fonte.
- Conteúdo: módulo mais utilizado, serve para inserir textos como notícias e artigos.
- Galeria de Fotos: usado quando precisa agrupar imagens em galerias.
- Imagens: cadastro de imagens separadamente, como banners e destaques.
- Pesquisa de Marketing: utilizado para enquete e questionários maiores.
- Privilégios: conecta-se aos conteúdos de outros módulos para prover
			privilégios a usuários cadastrados.
- YouTube: usado para cadastrar vídeos do YouTube, com preview no formulário de edição.
