import pandas as pd

reader = pd.read_csv('/Users/gyankou/Desktop/Prox_TS/output_file4.txt',sep=',')
writer = reader[reader['Packets_Type']=='ICMP_tt']
writer.to_csv('newfile.csv', index=False)import pandas as pd