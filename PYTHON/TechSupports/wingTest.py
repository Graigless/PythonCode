import xlwings as xw
wb = xw.Book()  # this will open a new workbook
#wb = xw.Book('FileName.xlsx')  # connect to a file that is open or in the current working directory
#wb = xw.Book(r'C:\path\to\file.xlsx')  # on Windows: use raw strings to escape backslashes