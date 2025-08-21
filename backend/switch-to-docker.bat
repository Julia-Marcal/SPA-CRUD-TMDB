@echo off
echo Switching to Docker environment...
if exist .env (
    ren .env .env.local
)
if exist .env.docker (
    copy .env.docker .env
    echo Successfully switched to Docker environment (DB_HOST=db)
) else (
    echo .env.docker not found!
)
pause
