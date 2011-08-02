## Estruturas de Conteúdo

O Aust é composto por Módulos, enquanto seu site é composto por Estruturas.

Um __Módulo__ possui as funcionalidades, formulários e mecanismos para uma estrutura.
Uma __Estrutura__ é uma seção do seu site, como Notícias, Artigos, Imagens, entre outros.


O AustCMS foi iniciado em set/2008. Sua arquitetura é baseada na
seguinte estrutura:

       ----
      |CORE|----- Módulo A
       ----
        |  \
        |   \
        |    \
        |     --- Módulo B --- Módulo-Embed
        |            \
      Módulo C         ------ Módulo-Embed

No caso da estrutura Notícias, ela usará o módulo Textual, que possui os formulários já prontos.


MÓDULOS ATUAIS
---------------------------------------

Atualmente, o sistema conta com os seguintes módulos:

- Agenda: possui interface para controlar datas e horários.
- Files: possibilidade de upload de arquivos.
- FlexibleFields: este módulo possui capacidade de criar tabelas e campos, de forma
			que pode-se reconstruir virtualmente qualquer domínio sem necessidade
			de tocar em códigos-fonte.
- Textual: módulo mais utilizado, serve para inserir textos como notícias e artigos.
- PhotoGallery: usado quando precisa agrupar imagens em galerias.
- Images: cadastro de imagens separadamente, como banners e destaques.

Em desenvolvimento:

- Pesquisa de Marketing (alpha): utilizado para enquete e questionários maiores.
- YouTube (alpha): usado para cadastrar vídeos do YouTube, com preview no formulário de edição.
