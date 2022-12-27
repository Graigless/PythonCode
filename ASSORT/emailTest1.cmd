setlocal ENABLEDELAYEDEXPANSION
get-content email.txt -Encoding UTF8 | Set-Content poutput.txt