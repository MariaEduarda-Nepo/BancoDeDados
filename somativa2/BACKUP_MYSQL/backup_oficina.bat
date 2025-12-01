@echo off
:: ============================================================
:: CONFIGURAÇÕES DO BACKUP
:: ============================================================

:: 1. Defina o usuário e senha do banco
set DB_USER=root
set DB_PASS=senaisp

:: 2. Nome do banco de dados
set DB_NAME=oficina

:: 3. Onde salvar o backup (Crie essa pasta antes!)
set BACKUP_PATH=C:\Users\2devt\Desktop\BCD\BancoDeDados\somativa2\BACKUP_MYSQL

:: 4. Caminho para o executável mysqldump
:: (Verifique se o caminho abaixo está correto para sua versão do MySQL)
set MYSQLDUMP_PATH="C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqldump.exe"

:: ============================================================
:: GERAÇÃO DO ARQUIVO
:: ============================================================

:: Gera um nome de arquivo com data e hora (Ex: oficina_pro_2023-10-27_15-30.sql)
set DATETIME=%date:~6,4%-%date:~3,2%-%date:~0,2%_%time:~0,2%-%time:~3,2%
set DATETIME=%DATETIME: =0%
set FILE_NAME=%BACKUP_PATH%\%DB_NAME%_%DATETIME%.sql

echo Iniciando backup de %DB_NAME%...

:: Executa o comando de backup
%MYSQLDUMP_PATH% --user=%DB_USER% --password=%DB_PASS% --databases %DB_NAME% --routines --triggers --events --result-file="%FILE_NAME%"

if %ERRORLEVEL% equ 0 (
    echo Backup realizado com sucesso: %FILE_NAME%
) else (
    echo Erro ao realizar o backup!
)

:: Pausa para você ver o resultado (remova a linha 'pause' se for agendar no Windows)
pause
