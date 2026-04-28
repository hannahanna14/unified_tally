@echo off
echo Starting Unified Tally System (MOPH - Manticao)...
echo Server running at http://localhost:8000
echo Press Ctrl+C to stop the server.

start http://localhost:8000
php -S localhost:8000
pause
