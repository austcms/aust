Atenção: Versão Alpha. Não utilize em produção.

### Aust CMS

Aust é um CMS modular open source que segue uma abordagem moderna e mais eficiente para
o gerenciamento de conteúdos de sites. Completamente separado do site principal, o
Aust propicia maior liberdade criativa ao desenvolvimento enviando conteúdos à
sua página através de uma API JSON.

Por entregar conteúdo através de uma API, integra-se com sites em qualquer linguagem. Foi desenvolvido em PHP.

Leia o Wiki para mais informações.

### Como instalar e usar

1. Faça o download do Aust e coloque-o em um diretório do seu servidor
2. Crie um banco de dados ('aust', por exemplo). Renomeie o arquivo config/database.sample.php
para config/database.php e configure a sua conexão
3. Acesse o Aust no seu navegador (ex.: http://localhost/aust). Sem você perceber,
o Aust vai instalar todas as tabelas necessárias e pedir que você configure um usuário
4. Você será levado à tela de login. Após entrar no Aust, você configure estruturas e módulos no
link _Configurar Módulos_ no rodapé.

### Criando um pequeno sistema de notícias

Vamos criar um sistema de notícias em menos de 2 minutos, sem tocar em código.

1. Acesse _Configurar Módulos_ no rodapé. Esta é a tela onde você controla todas as estruturas do Aust.
2. No módulo _Flexible Fields_, à direita, clique em _Instalar agora_.
3. Na esquerda, em _Instalar Estrutura_, selecione o Módulo Flexible Fields e no campo
_Nome da Estrutura_ escreva _Notícias_.
4. Na tela de instalação, no campo _Quantos campos terá seu cadastro?_, selecione 2 e clique em Enviar.
5. No linha 1, escreva Título em _Nome do Campo_. Na linha 2, escreve Texto e escolha Texto Grande em _Tipo de Campo_. Clique em Enviar.

Agora, vá até a aba **Gerenciar Contéudo** e você verá a estrutura _Notícias_. Você pode criar novos textos editá-los.

**Onde o Aust realmente brilha:** volte à tela de configuração de módulos e em _Estruturas Instaladas_,
clique em _Configurar_ ao lado de Notícias. Na nova tela, na seção _Configurações de Campos_, selecione
**Ativar editor de texto rico** e clique em Salvar. Volte ao formulário para ver o resultado.

### Acessando os conteúdos via API

Acesse no seu navegador http://endereco-do-aust/api/api.json?query=noticias. Basta você ler estes dados no seu site.

Antes dos lançamento do Aust na v0.3, vamos trabalhar em algumas bibliotecas para a leitura desta API.

### Licença

Leia o arquivo LICENSE para conhecer a licença de uso do Aust CMS.
