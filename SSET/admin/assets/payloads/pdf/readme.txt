### PDF User Instructions
Note: Because Javascript does not have permissions to know things about the target such as the username you must pass in a username as an argument when generating the file. This means that you should only send one file to one user and generate a new file for every person you wish to target.

- Open Powershell
- cd to the place where you saved the folder
e.g:
```
cd C:\Users\username\Desktop\PDF
```
- Run the script:
```
powershell -executionpolicy bypass -File .\pdf_gen.ps1 -username JoeBloggs
```
- The pdf document will be generated called payload.pdf, rename it and send away.
