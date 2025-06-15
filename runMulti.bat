@echo off
:: %cd% refers to the current working directory (variable)
:: %~dp0 refers to the full path to the batch file's directory (static)
:: %~dpnx0 and %~f0 both refer to the full path to the batch directory and file name (static).

SET "currentDir=%~dp0"
SET "script=%currentDir%downloadMulti.php"
echo %currentDir%
echo %script%

start php -f %script%
echo FINISH
pause
EXIT /B 0