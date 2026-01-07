# QR BusGuard - Setup & Run Guide

## 1. Prerequisites
- **XAMPP Installed** (for PHP & MySQL).
- **VS Code Installed** (for code editing).
- **Python Installed** (for AI matching).

## 2. How to Open in VS Code
1. Open VS Code.
2. Go to **File** > **Open Folder**.
3. Select `C:\xampp\htdocs\Project`.

## 3. How to Run the Project
VS Code edits the code, but **XAMPP runs it**.

1. Open **XAMPP Control Panel**.
2. Click **Start** for **Apache** and **MySQL**.
3. Open your browser (Chrome/Edge).
4. Go to: [http://localhost/Project](http://localhost/Project)

## 4. Run AI Scripts (Optional Check)
To check if AI is working, open a terminal in VS Code (`Ctrl + ~`) and run:
```bash
python python/image_match.py
```
(It may show an error about missing arguments, which is normal. It means Python is linked.)

## 5. Troubleshooting
- If "Site can't be reached", make sure Apache is GREEN in XAMPP.
- If Database error, check `db_connect.php` password (default is empty for root).
