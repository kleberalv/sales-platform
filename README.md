# Projeto de cadastro de clientes, produtos e vendas
Projeto feito com laravel e docker.

## Descrição
Esse projeto tem o objetivo da criação de uma plataforma para registro de clientes, produtos, vendas e o relatório de cada venda efetuada. O projeto foi desenvolvido para a CorpSystem como um teste prático para a vaga de desenvolvedor.

Detalhes de cada contêiner:

1. app:

Este contêiner é responsável por hospedar a aplicação principal desenvolvida em PHP. Ele é construído a partir de um Dockerfile personalizado que foi configurado para operar em conjunto com o servidor PHP. O uso do Xdebug facilita as atividades de desenvolvimento e depuração, tornando o processo mais eficiente.

2. db:

O contêiner MariaDB é dedicado ao armazenamento dos dados do aplicativo. Ele oferece um ambiente seguro para a persistência de informações cruciais, como tabelas e registros. A presença desse contêiner é fundamental para garantir o funcionamento correto e a integridade dos dados.

3. phpmyadmin:

Este contêiner hospeda uma interface web do phpMyAdmin, uma ferramenta de administração de banco de dados. Através dele, é possível interagir com o banco de dados MariaDB de maneira intuitiva e conveniente, facilitando tarefas administrativas e manipulação de dados.

4. nginx:

Atuando como servidor web, o contêiner Nginx direciona as solicitações HTTP para a aplicação PHP. Além disso, ele proporciona uma camada extra de segurança e otimização de desempenho, contribuindo para uma experiência de usuário mais eficiente e segura.

## Licença

Este projeto é licenciado sob a [Licença MIT](LICENSE). Consulte o arquivo [LICENSE](LICENSE) para obter mais detalhes.

### Responsabilidade

O autor deste projeto não assume nenhuma responsabilidade pelo uso indevido ou violação dos termos de licença. Você é o único responsável por garantir o uso adequado e ético deste código-fonte.

### Isenção de Garantia

Este projeto é fornecido "no estado em que se encontra", sem garantias de qualquer tipo. O autor não se responsabiliza por quaisquer danos ou consequências decorrentes do uso deste software.

## Instruções

Siga as etapas abaixo para configurar e executar o projeto:

1. Clone o repositórios sales-platform em seu sistema.

2. Abra um terminal de sua preferência e navegue até o diretório onde foi clonado o projeto sales-platform. OBS: O terminal do VSCode NÃO funcionará para abrir o container do passo 5. Recomendo utilizar um terminal externo.

3. Copie os dados do arquivo .env.example, crie um arquivo na raiz do projeto chamado .env e cole dentro dele os dados presentes em .env.example.

4. Execute o seguinte comando para construir e iniciar os contêineres:
`docker-compose up -d --build`

5. Após o download e a criação dos contêineres, acesse o contêiner app através do terminal:
`docker exec -it application-server-app /bin/bash`

6. Dentro do contêiner app, execute os seguintes comandos:
`composer install` e `php artisan migrate && php artisan db:seed`

7. Agora, você pode acessar o projeto em seu navegador através do link: http://localhost:8080/. O e-mail de acesso é `admin@example.com` e a senha é `password`.

8. Para acessar o banco de dados, acesse em seu navegador o link: http://localhost:8090/. Usuário e senhas foram definidas no .env

## Tecnologias utilizadas
<div align="left">
    <img align="center" alt="PHP" src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white">
    <img align="center" alt="Laravel" src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white">
    <img align="center" alt="Xdebug" src="https://img.shields.io/badge/Xdebug-DB1F29?style=for-the-badge&logo=xdebug&logoColor=white">
    <img align="center" alt="Bootstrap" src="https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white">
    <img align="center" alt="jQuery" src="https://img.shields.io/badge/jQuery-0769AD?style=for-the-badge&logo=jquery&logoColor=white">
    <img align="center" alt="JavaScript" src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black">
    <img align="center" alt="AJAX" src="https://img.shields.io/badge/AJAX-007396?style=for-the-badge&logo=ajax&logoColor=white">
</div>

## Ferramentas de desenvolvimento utilizadas
<div align="left">
    <img align="center" alt="Docker" src="https://img.shields.io/badge/docker-%230db7ed.svg?style=for-the-badge&logo=docker&logoColor=white"> 
    <img align="center" alt="Git" src="https://img.shields.io/badge/git-%23F05033.svg?style=for-the-badge&logo=git&logoColor=white"> 
    <img align="center" alt="Composer" src="https://img.shields.io/badge/Composer-885630?style=for-the-badge&logo=composer&logoColor=white">
    <img align="center" alt="MariaDB" src="https://img.shields.io/badge/MariaDB-003545?style=for-the-badge&logo=mariadb&logoColor=white">
    <img align="center" alt="phpMyAdmin" src="https://img.shields.io/badge/phpMyAdmin-4479A1?style=for-the-badge&logo=phpmyadmin&logoColor=white">
</div>

# Contato

1. kleberjuniorr63@gmail.com
2. https://www.linkedin.com/in/kleberalv/

# Copyright ©
Copyright © Developed by: Kleber Alves Bezerera Junior - Sênior Developer 2024.