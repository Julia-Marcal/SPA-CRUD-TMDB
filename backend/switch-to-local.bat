@echo off
echo Switching to Local environment...
if exist .env (
    ren .env .env.docker
)
if exist .env.local (
    copy .env.local .env
    echo Successfully switched to Local environment (DB_HOST=127.0.0.1)
) else (
    echo .env.local not found!
)
pause
